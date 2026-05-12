<?php
// PHP Setup: Minimal server-side logic, mostly for environment configuration.
$api_url = 'api.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - JRMSU SSG E-Voting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Inter', 'sans-serif'], },
                colors: { 'primary': '#001f3f', 'secondary': '#DAA520', 'bg-soft': '#f8fafc', 'navy-light': '#003366', },
            },
        },
    };
</script>
   <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
    #sidebar { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); min-width: 260px; background: linear-gradient(180deg, #001f3f 0%, #001021 100%); box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1); }
    #sidebar.open { transform: translateX(0) !important; }
    .sidebar-link { margin: 0.25rem 0.75rem; padding: 0.75rem 1rem; border-radius: 0.75rem; transition: all 0.2s ease; color: rgba(255, 255, 255, 0.7); }
    .sidebar-link:hover { background: rgba(255, 255, 255, 0.1); color: #ffffff; transform: translateX(5px); }
    .sidebar-link.active { background: #DAA520; color: #ffffff; font-weight: 600; box-shadow: 0 4px 12px rgba(218, 165, 32, 0.3); }
    .stat-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); @apply p-6 rounded-2xl shadow-sm border-l-4 border-primary; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-5px); @apply shadow-md; }
    .table-header { @apply px-6 py-4 text-left text-xs font-bold uppercase tracking-widest bg-slate-50; color: #001f3f; border-bottom: 2px solid #e2e8f0; }
    .btn-primary { background: #001f3f; @apply text-white px-6 py-2 rounded-xl font-bold transition-all shadow-lg hover:bg-secondary active:scale-95; }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #001f3f; border-radius: 10px; }
</style>
</head>
<body class="text-gray-800">

    <div class="flex min-h-screen">
        <aside id="sidebar" class="w-64 bg-primary text-white p-6 shadow-lg fixed md:relative inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-40">
            <div class="flex items-center justify-between mb-8">
               <div class="flex items-center">
                    <div class="h-10 w-10 bg-white rounded-lg flex items-center justify-center shadow-lg overflow-hidden">
                        <img src="OIP.jpg" alt="JRMSU Logo" class="h-full w-full object-cover">
                    </div>
                    <div class="ml-3"><span class="block text-xl font-bold leading-none text-white">JRMSU</span><span class="text-[10px] uppercase tracking-[0.2em] text-secondary font-bold">Siocon Campus</span></div>
                </div>
                <button id="close-sidebar" class="md:hidden text-white hover:text-secondary"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg></button>
            </div>
            
            <nav>
                <ul class="space-y-3" id="nav-links">
                    <li><a data-view="dashboard" class="flex items-center space-x-3 p-2 rounded-lg sidebar-link active cursor-pointer"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg><span>Dashboard</span></a></li>
                    <li><a data-view="voters" class="flex items-center space-x-3 p-2 rounded-lg sidebar-link cursor-pointer"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.131-4.131m0 0a3 3 0 0 0-4.131 4.131m4.131 0a3 3 0 0 0-4.131 4.131m0 0a3 3 0 0 0 4.131-4.131m-4.131-4.131a3 3 0 0 0-4.131-4.131m0 0a3 3 0 0 0-4.131 4.131m4.131 0a3 3 0 0 0 4.131-4.131M12 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /></svg><span>Voters</span></a></li>
                    <li><a data-view="candidates" class="flex items-center space-x-3 p-2 rounded-lg sidebar-link cursor-pointer"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.5 1.5 0 0 1 18 21.75H6a1.5 1.5 0 0 1-1.499-1.632Z" /></svg><span>Candidates</span></a></li>
                    <li><a data-view="positions" class="flex items-center space-x-3 p-2 rounded-lg sidebar-link cursor-pointer"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg><span>Positions</span></a></li>
                    <li><a data-view="results" class="flex items-center space-x-3 p-2 rounded-lg sidebar-link cursor-pointer"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 9.75 19.875V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg><span>Results</span></a></li>
                    <li><a data-view="settings" class="flex items-center space-x-3 p-2 rounded-lg sidebar-link cursor-pointer"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738a1.125 1.125 0 0 1-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.78.929l-.15.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg><span>Election Time</span></a></li>
                    <li><a href="logout.php" data-view="" class="flex items-center space-x-3 p-2 rounded-lg sidebar-link cursor-pointer text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg><span>Logout</span></a></li>
                </ul>
            </nav>

            <div class="absolute bottom-0 left-0 w-full p-4 border-t border-t-secondary text-xs text-gray-300">
                <p>Status: <span id="auth-status">MySQL/PHP Active</span></p>
                <p>User ID: <span id="current-user-id">ADMIN (Local)</span></p>
            </div>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden"></div>

        <main class="flex-1 p-4 md:p-10 overflow-x-auto w-full">
            <header class="flex items-center justify-between mb-8 flex-wrap gap-4">
                
                <div class="flex items-center space-x-3 md:space-x-4">
                    <img src="e402c0ae-139a-47d0-8fa7-e3b9d40864f1.jpg" alt="SSG Logo" class="h-12 w-auto md:h-20 object-contain drop-shadow-md">
                    <div>
                        <h1 class="text-xl md:text-3xl font-bold text-primary" id="main-header-title">Admin Dashboard</h1>
                        <p class="text-xs md:text-base text-gray-500" id="main-header-subtitle">Welcome to the JRMSU Siocon SSG E-Voting System.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button id="open-sidebar" class="md:hidden p-2 rounded-md text-primary bg-white shadow"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg></button>
                    <div class="hidden md:flex items-center space-x-4">
                        <div id="voting-status-display" class="mr-4"></div>
                        <span class="font-medium">Admin User</span>
                        <div class="h-10 w-10 rounded-full bg-secondary text-white flex items-center justify-center font-bold">A</div>
                    </div>
                </div>
                
                <div class="w-full md:hidden flex justify-end">
                    <div id="voting-status-display-mobile"></div>
                </div>
            </header>

            <div id="dashboard-view" class="app-view">
                <div id="loading-indicator" class="text-center p-8 text-lg font-medium text-secondary"><p>Loading data from MySQL...</p></div>
                <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8" id="dashboard-stats">
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex items-center space-x-4"><div class="p-3 rounded-full bg-blue-100 text-blue-600"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.131-4.131m0 0a3 3 0 0 0-4.131 4.131m4.131 0a3 3 0 0 0-4.131 4.131m0 0a3 3 0 0 0 4.131-4.131m-4.131-4.131a3 3 0 0 0-4.131-4.131m0 0a3 3 0 0 0-4.131 4.131m4.131 0a3 3 0 0 0 4.131-4.131M12 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /></svg></div><div><p class="text-sm text-gray-500">Total Voters</p><p id="stat-total-voters" class="text-2xl font-bold text-primary">0</p></div></div>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex items-center space-x-4"><div class="p-3 rounded-full bg-green-100 text-green-600"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.5 1.5 0 0 1 18 21.75H6a1.5 1.5 0 0 1-1.499-1.632Z" /></svg></div><div><p class="text-sm text-gray-500">Total Candidates</p><p id="stat-total-candidates" class="text-2xl font-bold text-primary">0</p></div></div>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex items-center space-x-4"><div class="p-3 rounded-full bg-yellow-100 text-yellow-600"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></div><div><p class="text-sm text-gray-500">Positions</p><p id="stat-total-positions" class="text-2xl font-bold text-primary">0</p></div></div>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex items-center space-x-4"><div class="p-3 rounded-full bg-indigo-100 text-indigo-600"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h-.75A2.25 2.25 0 0 0 4.5 9.75v7.5a2.25 2.25 0 0 0 2.25 2.25h.75m0-12s.75 0 1.5 0h8.25c.828 0 1.5.672 1.5 1.5v6c0 .828-.672 1.5-1.5 1.5H9.75m-2.25 0a2.25 2.25 0 0 1-2.25-2.25v-7.5a2.25 2.25 0 0 1 2.25-2.25h.75" /></svg></div><div><p class="text-sm text-gray-500">Unique Voters Cast</p><p id="stat-total-votes" class="text-2xl font-bold text-primary">0</p></div></div>
                </section>
                <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg border border-gray-100"><h2 class="text-xl font-semibold text-primary mb-4">Voter Turnout Ratio</h2><div class="flex justify-center h-80 w-full"><canvas id="turnoutChart"></canvas></div></div>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100"><h2 class="text-xl font-semibold text-primary mb-4">Latest Votes Cast</h2><ul class="space-y-4 max-h-96 overflow-y-auto" id="recent-activity-list"><li class="text-gray-400">No recent votes yet.</li></ul></div>
                </section>
            </div>

            <div id="voters-view" class="app-view" style="display:none;">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 w-full">
                    <h2 class="text-2xl font-semibold text-secondary">Registered Voters</h2>
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                        <button class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200 flex items-center justify-center" onclick="resetAllVotes()"><svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>Reset All Votes</button>
                        <button class="w-full sm:w-auto bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200 flex items-center justify-center" onclick="openVoterModal()">+ Add Voter</button>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg overflow-x-auto w-full">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead><tr><th class="table-header text-center w-16">ID</th><th class="table-header text-left">Student ID</th><th class="table-header text-left">Full Name</th><th class="table-header text-left hidden sm:table-cell">Course/Year</th><th class="table-header text-center">Status</th><th class="table-header text-center w-32">Actions</th></tr></thead>
                        <tbody id="voters-table-body" class="bg-white divide-y divide-gray-200"><tr><td colspan="6" class="table-cell text-center text-gray-400">Loading voters...</td></tr></tbody>
                    </table>
                </div>
            </div>

            <div id="candidates-view" class="app-view" style="display:none;">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 w-full"><h2 class="text-2xl font-semibold text-secondary">Registered Candidates</h2><button class="w-full sm:w-auto bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="openCandidateModal()">+ Add Candidate</button></div>
                <div class="bg-white rounded-xl shadow-lg overflow-x-auto w-full">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead><tr><th class="table-header text-center w-16 hidden sm:table-cell">ID</th><th class="table-header text-center w-24">Profile</th> <th class="table-header text-left">Name</th><th class="table-header text-left">Position</th><th class="table-header text-left hidden md:table-cell">Party</th><th class="table-header text-center w-32">Actions</th></tr></thead>
                        <tbody id="candidates-table-body" class="bg-white divide-y divide-gray-200"><tr><td colspan="6" class="table-cell text-center text-gray-400">Loading candidates...</td></tr></tbody>
                    </table>
                </div>
            </div>

            <div id="positions-view" class="app-view" style="display:none;">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 w-full"><h2 class="text-2xl font-semibold text-secondary">Electoral Positions</h2><button class="w-full sm:w-auto bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="openPositionModal()">+ Add Position</button></div>
                <div class="bg-white rounded-xl shadow-lg overflow-x-auto w-full">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead><tr><th class="table-header text-center w-16 hidden sm:table-cell">ID</th><th class="table-header text-left">Position Name</th><th class="table-header text-center">Max Votes</th><th class="table-header text-center w-32">Actions</th></tr></thead>
                        <tbody id="positions-table-body" class="bg-white divide-y divide-gray-200"><tr><td colspan="4" class="table-cell text-center text-gray-400">Loading positions...</td></tr></tbody>
                    </table>
                </div>
            </div>

            <div id="results-view" class="app-view" style="display:none;">
                <h2 class="text-2xl font-semibold text-secondary mb-6">Live Election Results</h2>
                <div id="results-container" class="space-y-8"><p class="text-gray-500 text-center p-8 bg-white rounded-xl shadow-lg">Fetching results data...</p></div>
            </div>

            <div id="settings-view" class="app-view" style="display:none;">
                <div class="flex justify-between items-center mb-6"><h2 class="text-2xl font-semibold text-secondary">Election Schedule Settings</h2></div>
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 max-w-lg w-full">
                    <p class="text-sm text-gray-500 mb-6">Set the specific timeframe during which voters are allowed to cast their ballots. Once the current time is outside of this window, the voting automatically closes.</p>
                    <form id="settings-form" onsubmit="saveElectionSettings(event)">
                        <div class="mb-4"><label class="block text-gray-700 text-sm font-bold mb-2" for="start_time">Voting Start Time</label><input type="datetime-local" id="start_time" name="start_time" class="p-3 border border-gray-300 rounded-lg w-full focus:ring-secondary focus:border-secondary transition-colors" required></div>
                        <div class="mb-8"><label class="block text-gray-700 text-sm font-bold mb-2" for="end_time">Voting End Time</label><input type="datetime-local" id="end_time" name="end_time" class="p-3 border border-gray-300 rounded-lg w-full focus:ring-secondary focus:border-secondary transition-colors" required></div>
                        <div class="flex justify-end"><button type="submit" id="save-settings-btn" class="w-full sm:w-auto bg-primary hover:bg-secondary text-white font-bold py-2 px-6 rounded-lg shadow-md transition-colors duration-200">Save Schedule</button></div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <div id="data-modal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 opacity-0 pointer-events-none">
        <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-2xl transform scale-95 transition-all duration-300" id="data-modal-content">
            <h3 class="text-2xl font-bold text-primary mb-4" id="modal-title">Add New Item</h3>
            <form id="data-form" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="form-item-id">
                <input type="hidden" id="form-collection-name">
                <div id="form-fields" class="space-y-4 mb-6"></div>
                <div class="flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors" onclick="closeModal('data-modal')">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-secondary text-white font-semibold rounded-lg hover:bg-primary transition-colors duration-200" id="form-submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="confirm-modal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 opacity-0 pointer-events-none">
        <div class="bg-white w-full max-w-sm p-6 rounded-xl shadow-2xl transform scale-95 transition-all duration-300">
            <h3 class="text-xl font-bold text-red-600 mb-3" id="confirm-modal-title">Confirm Action</h3>
            <p id="confirm-message" class="text-gray-700 mb-6">Are you sure you want to perform this action? This cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors" onclick="closeModal('confirm-modal')">Cancel</button>
                <button type="button" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors duration-200" id="confirm-action-btn">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '<?php echo $api_url; ?>'; 
        const POLLING_INTERVAL = 5000; 

        let voters = [], candidates = [], positions = [], votes = [], uniqueVotersCast = new Set(), voterStatusMap = {}, resultsData = [];
        let electionSettings = { start_time: null, end_time: null };
        let turnoutChart;

        const fileToBase64 = file => new Promise((resolve, reject) => {
            const reader = new FileReader(); reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result); reader.onerror = error => reject(error);
        });

        function formatMySQLTimestamp(timestamp) {
            if (!timestamp) return 'N/A';
            const date = new Date(timestamp.replace(/-/g, "/")); 
            return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) + ', ' + date.toLocaleDateString('en-US', { day: 'numeric', month: 'short' });
        }
        
        async function callApi(params, method = 'GET', body = null) {
            const url = new URL(API_URL, window.location.href);
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
            const options = { method: method, headers: { 'Content-Type': 'application/json' } };
            if (body && (method === 'POST' || method === 'PUT' || method === 'DELETE')) options.body = JSON.stringify(body);
            try {
                const response = await fetch(url.toString(), options);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();
                if (!result.success) { console.error("API Error:", result.message); return null; }
                return result.data || true; 
            } catch (error) { console.error("Fetch API failed:", error); return null; }
        }
        
        async function fetchAllData() {
            try {
                const [voterData, candidateData, positionData, voteData, resultData, settingsData] = await Promise.all([
                    callApi({ action: 'get', collection: 'voters' }), callApi({ action: 'get', collection: 'candidates' }),
                    callApi({ action: 'get', collection: 'positions' }), callApi({ action: 'get', collection: 'votes' }),
                    callApi({ action: 'results', collection: 'votes' }), callApi({ action: 'get_settings' })
                ]);

                if (voterData && Array.isArray(voterData)) voters = voterData.map(v => ({...v, id: String(v.id)}));
                if (candidateData && Array.isArray(candidateData)) candidates = candidateData.map(c => ({...c, id: String(c.id), positionId: String(c.position_id)}));
                if (positionData && Array.isArray(positionData)) positions = positionData.map(p => ({...p, id: String(p.id), maxVotes: Number(p.max_votes)}));
                if (voteData && Array.isArray(voteData)) votes = voteData.map(v => ({...v, id: String(v.id), voterId: String(v.voter_id), positionId: String(v.position_id), candidateId: String(v.candidate_id)}));
                else votes = [];
                
                uniqueVotersCast = new Set(votes.map(v => v.voterId));
                voterStatusMap = {};
                voters.forEach(v => { voterStatusMap[v.id] = (v.has_voted == 1) || uniqueVotersCast.has(v.id); });

                if (resultData && Array.isArray(resultData)) resultsData = resultData;
                else resultsData = [];

                if (settingsData) {
                    electionSettings = settingsData;
                    const isSettingsVisible = document.getElementById('settings-view').style.display !== 'none';
                    const startInput = document.getElementById('start_time');
                    const endInput = document.getElementById('end_time');
                    if (!isSettingsVisible || startInput.value === '') startInput.value = electionSettings.start_time || '';
                    if (!isSettingsVisible || endInput.value === '') endInput.value = electionSettings.end_time || '';
                    updateVotingStatusBadge();
                }
                
                renderDashboardStats();
                if (document.querySelector('.sidebar-link.active').getAttribute('data-view') === 'voters') renderVoters(voters);
                if (document.querySelector('.sidebar-link.active').getAttribute('data-view') === 'candidates') renderCandidates(candidates);
                if (document.querySelector('.sidebar-link.active').getAttribute('data-view') === 'positions') renderPositions(positions);
                if (document.querySelector('.sidebar-link.active').getAttribute('data-view') === 'results') renderResults();
            } catch (error) { console.error("Error updating all data:", error); }
        }

        async function saveElectionSettings(event) {
            event.preventDefault();
            const btn = document.getElementById('save-settings-btn');
            const originalText = btn.textContent; btn.textContent = "Saving..."; btn.disabled = true;
            const start_time = document.getElementById('start_time').value;
            const end_time = document.getElementById('end_time').value;
            const result = await callApi({}, 'POST', { action: 'save_settings', start_time: start_time, end_time: end_time });
            if (result) { await fetchAllData(); alert("Election schedule updated successfully."); } else { alert("Failed to save schedule."); }
            btn.textContent = originalText; btn.disabled = false;
        }

        function updateVotingStatusBadge() {
            const statusDiv = document.getElementById('voting-status-display');
            const statusDivMobile = document.getElementById('voting-status-display-mobile');
            if (!statusDiv) return;
            const now = new Date(); const start = electionSettings.start_time ? new Date(electionSettings.start_time) : null; const end = electionSettings.end_time ? new Date(electionSettings.end_time) : null;
            let statusText = 'Not Scheduled'; let statusColor = 'bg-gray-200 text-gray-800 border-gray-300';
            if (start && end) {
                if (now < start) { statusText = 'Voting Not Started'; statusColor = 'bg-yellow-100 text-yellow-800 border-yellow-300 shadow-sm'; } 
                else if (now >= start && now <= end) { statusText = 'Voting Open (Live)'; statusColor = 'bg-green-100 text-green-800 border-green-300 shadow-sm animate-pulse'; } 
                else if (now > end) { statusText = 'Voting Closed (Done)'; statusColor = 'bg-red-100 text-red-800 border-red-300 shadow-sm'; }
            }
            const innerHTMLContent = `<span class="px-4 py-1.5 rounded-full text-xs md:text-sm font-extrabold border ${statusColor} flex items-center shadow-inner"><span class="mr-2 h-2 w-2 rounded-full ${statusColor.includes('green') ? 'bg-green-600' : (statusColor.includes('red') ? 'bg-red-600' : 'bg-gray-500')}"></span>${statusText}</span>`;
            statusDiv.innerHTML = innerHTMLContent;
            if(statusDivMobile) statusDivMobile.innerHTML = innerHTMLContent;
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId); modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.querySelector('div:first-child').classList.remove('scale-95'); modal.querySelector('div:first-child').classList.add('scale-100');
        }
        function closeModal(modalId) {
            const modal = document.getElementById(modalId); modal.querySelector('div:first-child').classList.remove('scale-100');
            modal.querySelector('div:first-child').classList.add('scale-95'); modal.classList.add('opacity-0', 'pointer-events-none');
        }

        function openDataModal(collectionName, item = null) {
            const isEdit = !!item;
            document.getElementById('modal-title').textContent = isEdit ? `Edit ${collectionName.slice(0, -1).toUpperCase()}` : `Add New ${collectionName.slice(0, -1).toUpperCase()}`;
            document.getElementById('form-item-id').value = isEdit ? item.id : '';
            document.getElementById('form-collection-name').value = collectionName;
            document.getElementById('form-submit-btn').textContent = isEdit ? 'Update' : 'Save';
            const formFields = document.getElementById('form-fields'); formFields.innerHTML = ''; 

            let fields = [];
            if (collectionName === 'voters') { fields = [ { name: 'studentId', label: 'Student ID', type: 'text', required: true, value: item?.student_id || '' }, { name: 'name', label: 'Full Name', type: 'text', required: true, value: item?.name || '' }, { name: 'course', label: 'Course/Year', type: 'text', required: true, value: item?.course || '' } ]; } 
            else if (collectionName === 'candidates') { const positionOptions = positions.map(p => ({ value: p.id, label: p.name })); fields = [ { name: 'name', label: 'Candidate Name', type: 'text', required: true, value: item?.name || '' }, { name: 'partyList', label: 'Party/Affiliation', type: 'text', required: false, value: item?.party_list || '' }, { name: 'positionId', label: 'Electoral Position', type: 'select', required: true, options: positionOptions, value: item?.positionId || '' }, { name: 'image', label: 'Profile Image', type: 'file', accept: 'image/*', required: false } ]; if (item?.image) { document.getElementById('form-item-id').setAttribute('data-existing-image', item.image); } else { document.getElementById('form-item-id').removeAttribute('data-existing-image'); } } 
            else if (collectionName === 'positions') { fields = [ { name: 'name', label: 'Position Name', type: 'text', required: true, value: item?.name || '' }, { name: 'maxVotes', label: 'Max Votes for Position', type: 'number', required: true, value: item?.max_votes || 1 } ]; }

            fields.forEach(field => {
                const wrapper = document.createElement('div'); wrapper.className = 'flex flex-col';
                const label = document.createElement('label'); label.className = 'mb-1 font-medium text-sm text-gray-700'; label.textContent = field.label; wrapper.appendChild(label);
                let input;
                if (field.type === 'select') { input = document.createElement('select'); input.innerHTML = `<option value="" disabled selected>Select</option>`; field.options.forEach(option => { input.innerHTML += `<option value="${option.value}" ${field.value == option.value ? 'selected' : ''}>${option.label}</option>`; }); } 
                else if (field.type === 'file') { input = document.createElement('input'); input.type = field.type; if(field.accept) input.accept = field.accept; } 
                else { input = document.createElement('input'); input.type = field.type; input.value = field.value; }
                input.id = `form-${field.name}`; input.name = field.name; input.required = field.required; input.className = 'p-2 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary transition-colors';
                wrapper.appendChild(input); formFields.appendChild(wrapper);
            });
            openModal('data-modal');
        }

        async function handleFormSubmit(event) {
            event.preventDefault();
            const submitBtn = document.getElementById('form-submit-btn'); const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...'; submitBtn.disabled = true; submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            const itemId = document.getElementById('form-item-id').value; const collectionName = document.getElementById('form-collection-name').value;
            const isEdit = !!itemId; const itemData = {}; const fields = event.target.querySelectorAll('input, select');
            
            for (const field of fields) {
                if (field.name && field.name !== 'form-item-id' && field.name !== 'form-collection-name') {
                    if (field.type === 'file') { if (field.files.length > 0) { itemData[field.name] = await fileToBase64(field.files[0]); } else { itemData[field.name] = document.getElementById('form-item-id').getAttribute('data-existing-image') || null; } } 
                    else { itemData[field.name] = field.type === 'number' ? parseInt(field.value, 10) : field.value; }
                }
            }
            
            let action = isEdit ? 'update' : 'add';
            if (isEdit) itemData.id = itemId;
            const payload = { action: action, collection: collectionName, ...itemData };
            const result = await callApi({}, 'POST', payload);
            if (result) { await fetchAllData(); closeModal('data-modal'); }
            submitBtn.textContent = originalText; submitBtn.disabled = false; submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
        
        window.openVoterModal = () => openDataModal('voters');
        window.openCandidateModal = (item) => openDataModal('candidates', item);
        window.openPositionModal = (item) => openDataModal('positions', item);

        function deleteItem(collectionName, itemId, itemName) {
            openModal('confirm-modal');
            document.getElementById('confirm-modal-title').textContent = 'Confirm Deletion';
            document.getElementById('confirm-message').textContent = `Are you sure you want to remove ${collectionName.slice(0, -1)}: "${itemName}"? This action cannot be undone.`;
            
            const confirmBtn = document.getElementById('confirm-action-btn');
            confirmBtn.textContent = 'Confirm';
            confirmBtn.onclick = async () => {
                const originalText = confirmBtn.textContent; confirmBtn.textContent = 'Processing...'; confirmBtn.disabled = true; confirmBtn.classList.add('opacity-75', 'cursor-not-allowed');
                const result = await callApi({}, 'POST', { action: 'delete', collection: collectionName, id: itemId });
                if (result) { await fetchAllData(); closeModal('confirm-modal'); }
                confirmBtn.textContent = originalText; confirmBtn.disabled = false; confirmBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            };
        }

        async function approveVoter(voterId) {
            const result = await callApi({}, 'POST', { action: 'approve_voter', id: voterId });
            if (result) {
                await fetchAllData();
            }
        }
        window.approveVoter = approveVoter;

        function resetAllVotes() {
            openModal('confirm-modal');
            document.getElementById('confirm-modal-title').textContent = 'Reset Election Votes';
            document.getElementById('confirm-message').innerHTML = `Are you sure you want to <strong>reset ALL voting records</strong>? <br><br>This will clear all current election results and allow every registered voter to vote again. <strong>This action CANNOT be undone.</strong>`;
            
            const confirmBtn = document.getElementById('confirm-action-btn');
            confirmBtn.textContent = 'Confirm Reset';
            confirmBtn.onclick = async () => {
                const originalText = confirmBtn.textContent; confirmBtn.textContent = 'Resetting...'; confirmBtn.disabled = true; confirmBtn.classList.add('opacity-75', 'cursor-not-allowed');
                try {
                    const resetResult = await callApi({}, 'POST', { action: 'reset_votes' });
                    if(resetResult) { votes = []; uniqueVotersCast = new Set(); voterStatusMap = {}; resultsData = []; await fetchAllData(); closeModal('confirm-modal'); } 
                    else { alert("An error occurred while attempting to reset votes."); }
                } catch (error) { console.error("Failed to reset:", error); alert("Error resetting votes."); } 
                finally { confirmBtn.textContent = originalText; confirmBtn.disabled = false; confirmBtn.classList.remove('opacity-75', 'cursor-not-allowed'); }
            };
        }

        function renderDashboardStats() {
            const totalVoters = voters.length; const totalCandidates = candidates.length; const totalPositions = positions.length; const votedCount = uniqueVotersCast.size; const notVotedCount = totalVoters - votedCount;
            document.getElementById('stat-total-voters').textContent = totalVoters; document.getElementById('stat-total-candidates').textContent = totalCandidates; document.getElementById('stat-total-positions').textContent = totalPositions; document.getElementById('stat-total-votes').textContent = votedCount; document.getElementById('loading-indicator').style.display = 'none';

            if (turnoutChart) { turnoutChart.data.datasets[0].data = [votedCount, notVotedCount]; turnoutChart.update(); } 
            else {
                const ctx = document.getElementById('turnoutChart').getContext('2d');
                turnoutChart = new Chart(ctx, { type: 'pie', data: { labels: ['Voted', 'Remaining'], datasets: [{ data: [votedCount, notVotedCount], backgroundColor: ['#0A4D68', '#088395'], hoverBackgroundColor: ['#088395', '#0A4D68'], borderWidth: 1, }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: (context) => { return `${context.label}: ${context.parsed}`; } } } } } });
            }
            const recentActivityList = document.getElementById('recent-activity-list'); recentActivityList.innerHTML = '';
            if (votes.length === 0) { recentActivityList.innerHTML = '<li class="text-gray-400 text-center py-4">No votes have been cast yet.</li>'; return; }
            const latestVotes = votes.slice(0, 10);
            latestVotes.forEach(vote => {
                const voter = voters.find(v => v.id == vote.voterId); const position = positions.find(p => p.id == vote.positionId);
                const voterName = voter?.name || 'Unknown Voter'; const positionName = position?.name || 'Unknown Position';
                recentActivityList.innerHTML += `<li class="flex items-start space-x-3 p-2 bg-gray-50 rounded-lg hover:bg-light transition-colors"><div class="mt-1 p-2 rounded-full bg-indigo-100 text-indigo-600 flex-shrink-0"><svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h-.75A2.25 2.25 0 0 0 4.5 9.75v7.5a2.25 2.25 0 0 0 2.25 2.25h.75m0-12s.75 0 1.5 0h8.25c.828 0 1.5.672 1.5 1.5v6c0 .828-.672 1.5-1.5 1.5H9.75m-2.25 0a2.25 2.25 0 0 1-2.25-2.25v-7.5a2.25 2.25 0 0 1 2.25-2.25h.75" /></svg></div><div><p class="text-sm font-medium">Vote cast for ${positionName}</p><p class="text-xs text-gray-500">By: ${voterName}</p><p class="text-xs text-gray-400">${formatMySQLTimestamp(vote.created_at)}</p></div></li>`;
            });
        }

        function renderVoters(voterList) {
            const tableBody = document.getElementById('voters-table-body'); tableBody.innerHTML = '';
            if (voterList.length === 0) { tableBody.innerHTML = '<tr><td colspan="6" class="table-cell text-center text-gray-400 py-6">No voters registered yet.</td></tr>'; return; }
            voterList.forEach(voter => {
                const hasVoted = voterStatusMap[voter.id] || false;
                const isApproved = voter.is_approved == 1 || voter.is_approved === undefined; 
                
                let statusBadge = '';
                let actionBtns = '';

                if (!isApproved) {
                    statusBadge = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>`;
                    actionBtns = `
                        <button class="text-green-600 hover:text-green-800 font-bold transition-colors text-xs sm:text-sm" onclick='approveVoter("${voter.id}")'>Approve</button>
                        <button class="text-red-600 hover:text-red-800 font-bold transition-colors ml-2 text-xs sm:text-sm" onclick='deleteItem("voters", "${voter.id}", "${voter.name}")'>Disapprove</button>
                    `;
                } else {
                    statusBadge = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${hasVoted ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">${hasVoted ? 'Voted' : 'Not Voted'}</span>`;
                    actionBtns = `
                        <button class="text-blue-600 hover:text-blue-800 font-medium transition-colors text-xs sm:text-sm" onclick='openDataModal("voters", ${JSON.stringify(voter)})'>Edit</button>
                        <button class="text-red-600 hover:text-red-800 font-medium transition-colors ml-2 text-xs sm:text-sm" onclick='deleteItem("voters", "${voter.id}", "${voter.name}")'>Delete</button>
                    `;
                }

                tableBody.innerHTML += `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="table-cell text-xs text-gray-400 text-center">${voter.id}</td>
                        <td class="table-cell font-mono text-left text-sm md:text-base">${voter.student_id}</td>
                        <td class="table-cell font-medium text-left text-sm md:text-base">${voter.name}</td>
                        <td class="table-cell text-left hidden sm:table-cell text-sm md:text-base">${voter.course}</td>
                        <td class="table-cell text-center">${statusBadge}</td>
                        <td class="table-cell space-x-2 text-center">${actionBtns}</td>
                    </tr>
                `;
            });
        }

        function renderCandidates(candidateList) {
            const tableBody = document.getElementById('candidates-table-body'); tableBody.innerHTML = '';
            if (candidateList.length === 0) { tableBody.innerHTML = '<tr><td colspan="6" class="table-cell text-center text-gray-400 py-6">No candidates registered yet.</td></tr>'; return; }
            candidateList.forEach(candidate => {
                const position = positions.find(p => p.id == candidate.positionId)?.name || 'N/A';
                const imgTag = candidate.image ? `<img src="${candidate.image}" class="h-10 w-10 rounded-full object-cover border shadow-sm">` : `<div class="h-10 w-10 rounded-full bg-gray-200 border flex items-center justify-center text-gray-500 font-bold shadow-sm">${candidate.name.charAt(0)}</div>`;
                tableBody.innerHTML += `<tr class="hover:bg-gray-50 transition-colors"><td class="table-cell text-xs text-gray-400 text-center hidden sm:table-cell">${candidate.id}</td><td class="table-cell text-center flex justify-center py-2">${imgTag}</td> <td class="table-cell font-medium text-left text-sm md:text-base">${candidate.name}</td><td class="table-cell font-semibold text-secondary text-left text-sm md:text-base">${position}</td><td class="table-cell text-left hidden md:table-cell text-sm md:text-base">${candidate.party_list || 'Independent'}</td><td class="table-cell space-x-2 text-center"><button class="text-blue-600 hover:text-blue-800 font-medium transition-colors text-xs md:text-sm" onclick='openDataModal("candidates", ${JSON.stringify(candidate)})'>Edit</button></td></tr>`;
            });
        }

        function renderPositions(positionList) {
            const tableBody = document.getElementById('positions-table-body'); tableBody.innerHTML = '';
            if (positionList.length === 0) { tableBody.innerHTML = '<tr><td colspan="4" class="table-cell text-center text-gray-400 py-6">No electoral positions defined yet.</td></tr>'; return; }
            positionList.forEach(position => {
                tableBody.innerHTML += `<tr class="hover:bg-gray-50 transition-colors"><td class="table-cell text-xs text-gray-400 text-center hidden sm:table-cell">${position.id}</td><td class="table-cell font-medium text-primary text-left text-sm md:text-base">${position.name}</td><td class="table-cell text-center text-sm md:text-base">${position.maxVotes} vote${position.maxVotes > 1 ? 's' : ''}</td><td class="table-cell space-x-2 text-center"><button class="text-blue-600 hover:text-blue-800 font-medium transition-colors text-xs md:text-sm" onclick='openDataModal("positions", ${JSON.stringify(position)})'>Edit</button><button class="text-red-600 hover:text-red-800 font-medium transition-colors text-xs md:text-sm" onclick='deleteItem("positions", "${position.id}", "${position.name}")'>Delete</button></td></tr>`;
            });
        }
        
        function renderResults() {
            const resultsContainer = document.getElementById('results-container'); resultsContainer.innerHTML = '';
            if (votes.length === 0) { resultsContainer.innerHTML = '<p class="text-gray-500 text-center p-8 bg-white rounded-xl shadow-lg">No votes have been recorded yet.</p>'; return; }
            const groupedResults = resultsData.reduce((acc, result) => {
                const positionId = result.positionId;
                if (!acc[positionId]) { acc[positionId] = { positionName: result.positionName, maxVotes: result.maxVotes, candidates: [] }; }
                acc[positionId].candidates.push(result); return acc;
            }, {});
            Object.values(groupedResults).forEach(group => {
                const { positionName, maxVotes, candidates } = group; let winnerStatus = ''; let winnerName = 'TBD';
                if (candidates.length > 0) {
                    const topVoteCount = candidates[0].voteCount; const winners = candidates.filter(c => c.voteCount == topVoteCount && c.voteCount > 0);
                    if (winners.length === maxVotes) { winnerName = winners.map(w => w.candidateName).join(', '); winnerStatus = `<span class="text-green-600 font-bold">Winner(s) Found</span>`; } 
                    else if (winners.length > maxVotes) { winnerName = winners.map(w => w.candidateName).join(', '); winnerStatus = `<span class="text-yellow-600 font-bold">Tie</span>`; } 
                    else if (winners.length === 1 && maxVotes === 1 && winners[0].voteCount > 0) { winnerName = winners[0].candidateName; winnerStatus = `<span class="text-green-600 font-bold">Winner: ${winnerName}</span>`; } 
                    else { winnerStatus = `<span class="text-gray-500">Election Ongoing</span>`; }
                }
                let candidateRows = candidates.map((c, index) => {
                    const isWinner = c.voteCount === candidates[0].voteCount && c.voteCount > 0;
                    const avatarHTML = c.image ? `<img src="${c.image}" class="h-8 w-8 rounded-full object-cover mr-3 inline-block">` : `<div class="h-8 w-8 rounded-full bg-gray-200 inline-flex items-center justify-center text-gray-500 font-bold mr-3">${c.candidateName.charAt(0)}</div>`;
                    return `<tr class="${isWinner ? 'bg-accent/30 font-semibold' : index % 2 === 0 ? 'bg-white' : 'bg-gray-50'} transition-all hover:bg-light"><td class="table-cell text-center font-medium">${index + 1}</td><td class="table-cell flex items-center text-left text-sm md:text-base">${avatarHTML} ${c.candidateName}</td><td class="table-cell text-left text-sm md:text-base">${c.partyList || 'Independent'}</td><td class="table-cell text-xl md:text-2xl font-extrabold text-primary text-center">${c.voteCount}</td><td class="table-cell text-sm text-center">${isWinner && c.voteCount > 0 ? '<span class="text-green-600 font-bold text-xs md:text-sm">LEADER</span>' : ''}</td></tr>`;
                }).join('');
                resultsContainer.innerHTML += `<div class="bg-white p-4 md:p-6 rounded-xl shadow-lg border border-gray-100"><div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b pb-2 gap-2"><h3 class="text-lg md:text-xl font-bold text-primary">${positionName} (Max: ${maxVotes})</h3><p class="text-sm md:text-lg">${winnerStatus}</p></div><div class="overflow-x-auto w-full"><table class="min-w-full divide-y divide-gray-200"><thead><tr><th class="table-header w-12 text-center">Rank</th><th class="table-header text-left">Candidate</th><th class="table-header text-left">Party List</th><th class="table-header w-24 text-center">Votes</th><th class="table-header w-24 text-center">Status</th></tr></thead><tbody>${candidateRows}</tbody></table></div></div>`;
            });
        }

        function setupNavigation() {
            const navLinks = document.getElementById('nav-links'); const views = document.querySelectorAll('.app-view');
            function navigate(viewName) {
                if(!viewName) return;
                document.querySelectorAll('.sidebar-link').forEach(link => { link.classList.remove('active'); if (link.getAttribute('data-view') === viewName) link.classList.add('active'); });
                views.forEach(view => { view.style.display = view.id === `${viewName}-view` ? 'block' : 'none'; });
                const titles = { dashboard: ['Admin Dashboard', 'Welcome to the JRMSU Siocon SSG E-Voting System.'], voters: ['Voters Management', 'Manage student registration and voting status.'], candidates: ['Candidates Management', 'Manage all candidates and their affiliations.'], positions: ['Positions Management', 'Define the electoral positions.'], results: ['Live Election Results', 'Real-time tabulation.'], settings: ['Election Time Settings', 'Configure when students can access the ballot.'] };
                if (titles[viewName]) { document.getElementById('main-header-title').textContent = titles[viewName][0]; document.getElementById('main-header-subtitle').textContent = titles[viewName][1]; }
                if (viewName === 'voters') renderVoters(voters); if (viewName === 'candidates') renderCandidates(candidates); if (viewName === 'positions') renderPositions(positions); if (viewName === 'results') renderResults();
                closeSidebar();
            }
            navLinks.addEventListener('click', (e) => { const link = e.target.closest('.sidebar-link'); if (link && link.getAttribute('data-view')) navigate(link.getAttribute('data-view')); });
            const sidebar = document.getElementById('sidebar'); const openSidebarBtn = document.getElementById('open-sidebar'); const closeSidebarBtn = document.getElementById('close-sidebar'); const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            // Adjusted logic slightly to ensure overlay shows accurately on CP/Mobile
            function openSidebar() { sidebar.classList.add('open'); sidebarOverlay.classList.remove('hidden'); } 
            function closeSidebar() { sidebar.classList.remove('open'); sidebarOverlay.classList.add('hidden'); }
            
            openSidebarBtn.addEventListener('click', openSidebar); closeSidebarBtn.addEventListener('click', closeSidebar); sidebarOverlay.addEventListener('click', closeSidebar);
            window.handleFormSubmit = handleFormSubmit; window.closeModal = closeModal; window.deleteItem = deleteItem; window.resetAllVotes = resetAllVotes; window.saveElectionSettings = saveElectionSettings;
        }

        document.addEventListener('DOMContentLoaded', () => { setupNavigation(); fetchAllData(); setInterval(fetchAllData, POLLING_INTERVAL); });
    </script>
</body>
</html>
