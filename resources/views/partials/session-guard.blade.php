{{--
    Shared session guard for every screen that stays open on the counter
    (POS, exchange, stock intake, stock return).

    Terminals sit idle between customers, so the session used to lapse while
    the page kept its original CSRF token. The next submit then died with a
    419 "CSRF token mismatch" and the cashier lost the whole cart.

    Two jobs:
      1. Ping the server before the session can lapse and adopt the token it
         hands back, so an open screen never goes stale.
      2. If a submit still hits a 419, save the screen's state, reload, and
         pick it back up instead of showing a dead error.
--}}
<script>
    window.PosSession = {
        // Pages register these to persist / restore their own state.
        onExpired: null,
        onRestored: null,
        storageKey: null,

        _recovering: false,

        token() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        sync(token) {
            if (!token) return;
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', token);
        },

        async fetch(url, options = {}) {
            const headers = Object.assign({}, options.headers || {});
            headers['Accept'] = headers['Accept'] || 'application/json';
            headers['X-CSRF-TOKEN'] = this.token();
            headers['X-Requested-With'] = 'XMLHttpRequest';

            const res = await fetch(url, Object.assign({}, options, { headers }));

            if (res.status === 419) {
                this.recover();
                return res;
            }

            // The session lapsed and auth bounced us to the login screen.
            if (res.redirected) {
                window.location.href = '{{ route('login') }}';
                return res;
            }

            // Keep the meta tag current with whatever the server just issued.
            if (res.ok) {
                try {
                    const data = await res.clone().json();
                    this.sync(data && data.token);
                } catch (e) { /* response was not JSON */ }
            }

            return res;
        },

        async keepalive() {
            if (document.hidden) return;
            try {
                const res = await fetch('{{ route('session.keepalive') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                });

                if (res.status === 419 || res.redirected) return;

                const data = await res.json();
                this.sync(data && data.token);
            } catch (e) { /* offline - the next ping will catch up */ }
        },

        /**
         * Stash whatever this screen was holding, then reload. After login the
         * screen comes back up with the cart intact.
         */
        recover() {
            if (this._recovering) return;
            this._recovering = true;

            if (this.storageKey && typeof this.onExpired === 'function') {
                try { this.onExpired(); } catch (e) { /* nothing to save */ }
            }

            window.location.reload();
        },

        /**
         * Pull back whatever recover() stashed, then hand it to the page.
         * Call from the Alpine component's init().
         */
        restore() {
            if (!this.storageKey || typeof this.onRestored !== 'function') return;

            try {
                const raw = window.sessionStorage.getItem(this.storageKey);
                if (!raw) return;
                window.sessionStorage.removeItem(this.storageKey);
                this.onRestored(JSON.parse(raw));
            } catch (e) { /* stale or corrupt stash - ignore */ }
        },

        start(intervalMs = 240000) {
            // Coming back to the tab is the moment a stale screen is most likely.
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) this.keepalive();
            });

            window.addEventListener('focus', () => this.keepalive());

            setInterval(() => this.keepalive(), intervalMs);
        }
    };

    window.PosSession.start();
</script>