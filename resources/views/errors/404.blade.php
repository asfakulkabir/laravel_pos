<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]">
    <div class="max-w-md w-full text-center space-y-12">
        <!-- 404 Visual -->
        <div class="relative inline-block">
            <h1 class="text-[12rem] font-black leading-none tracking-tighter text-slate-200 select-none">
                404
            </h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-32 h-32 bg-indigo-600 rounded-full blur-3xl opacity-20 animate-pulse"></div>
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-4xl font-black text-slate-800 uppercase tracking-[1rem] ml-[1rem]">Lost</span>
            </div>
        </div>

        <!-- Messaging -->
        <div class="space-y-4">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Oops! Page Not Found</h2>
            <p class="text-slate-500 font-medium leading-relaxed">
                The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
            </p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col gap-3">
            <a href="{{ route('dashboard') }}" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Back to Dashboard
            </a>
            <a href="{{ route('pos.index') }}" class="w-full bg-white text-slate-600 py-4 rounded-2xl font-black text-sm uppercase tracking-widest border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                POS Terminal
            </a>
        </div>

        <!-- Footer -->
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">
            Mamata Fashion POS &bull; Error Code 404
        </p>
    </div>
</body>
</html>
