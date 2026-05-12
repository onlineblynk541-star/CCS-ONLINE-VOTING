<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official E-Voting Portal | JRMSU Siocon SSG</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], },
                    colors: { 'primary': '#001f3f', 'primary-light': '#003366', 'secondary': '#DAA520', 'secondary-dark': '#B8860B', 'accent': '#FFD700', },
                    animation: { 'fade-in-up': 'fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards', 'float': 'float 6s ease-in-out infinite', 'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite', },
                    keyframes: {
                        fadeInUp: { '0%': { opacity: '0', transform: 'translateY(30px)' }, '100%': { opacity: '1', transform: 'translateY(0)' }, },
                        float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-20px)' }, }
                    }
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; background-color: #f8fafc; }
        .hero-bg { background: linear-gradient(135deg, #001021 0%, #001f3f 50%, #003366 100%); position: relative; overflow: hidden; }
        .glow-orb { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.5; z-index: 0; pointer-events: none; }
        .orb-1 { top: -20%; left: -10%; width: 50vw; height: 50vw; background: rgba(218, 165, 32, 0.2); }
        .orb-2 { bottom: -20%; right: -10%; width: 40vw; height: 40vw; background: rgba(0, 80, 158, 0.4); }
        .logo-circle-container { border-radius: 9999px; display: flex; align-items: center; justify-content: center; background: white; overflow: hidden; }
    </style>
</head>
<body class="flex flex-col min-h-screen text-gray-800">

    <nav class="fixed w-full z-50 bg-primary/90 backdrop-blur-md border-b border-white/10 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="h-12 w-12 logo-circle-container border-2 border-secondary shadow-[0_0_15px_rgba(218,165,32,0.5)]"><img src="OIP.jpg" alt="JRMSU Logo" class="h-10 w-10 object-contain"></div>
                    <div><span class="block text-xl md:text-2xl font-black text-white tracking-wide leading-none">JRMSU</span><span class="text-[10px] md:text-xs uppercase tracking-[0.2em] text-secondary font-bold">Siocon Campus</span></div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-semibold text-gray-300 hover:text-white transition-colors">Features</a>
                    <a href="#guidelines" class="text-sm font-semibold text-gray-300 hover:text-white transition-colors">How to Vote</a>
                    <div class="flex items-center space-x-4 ml-4 pl-4 border-l border-white/20">
                        <a href="login.php" class="text-sm font-semibold text-gray-300 hover:text-secondary flex items-center transition-colors"><svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>Admin</a>
                        <a href="voter_dashboard.php" class="bg-gradient-to-r from-secondary to-accent text-primary px-6 py-2.5 rounded-full font-bold shadow-[0_4px_14px_0_rgba(218,165,32,0.39)] hover:shadow-[0_6px_20px_rgba(218,165,32,0.23)] hover:-translate-y-0.5 transition-all duration-200">Vote Now</a>
                    </div>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-300 hover:text-white focus:outline-none p-2"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path id="menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg></button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="md:hidden hidden bg-primary border-t border-white/10 absolute w-full transition-all duration-300 shadow-2xl">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="#features" class="block px-3 py-3 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/5">Features</a>
                <a href="#guidelines" class="block px-3 py-3 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/5">How to Vote</a>
                <div class="border-t border-white/10 my-2 pt-2">
                    <a href="login.php" class="block px-3 py-3 rounded-md text-base font-medium text-gray-300 hover:text-secondary hover:bg-white/5">Admin Login</a>
                    <a href="voter_dashboard.php" class="block mt-2 text-center bg-secondary text-primary px-3 py-3 rounded-lg text-base font-bold shadow-lg">Enter Voter Portal</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 hero-bg pt-20 flex items-center relative min-h-screen">
        <div class="glow-orb orb-1"></div><div class="glow-orb orb-2"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-12 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left space-y-8 animate-fade-in-up">
                    <div class="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-sm border border-white/20 px-4 py-2 rounded-full text-sm font-semibold text-accent shadow-sm">
                        <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-accent"></span></span><span>SSG Elections Currently Live</span>
                    </div>
                    
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight leading-[1.1]">Shape Your <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-secondary via-accent to-secondary animate-pulse-slow">Future Today.</span></h1>
                    <p class="text-lg sm:text-xl text-gray-300 max-w-2xl mx-auto lg:mx-0 font-light leading-relaxed">Welcome to the official WEB-BASED E-VOTING SYSTEM Portal of Jose Rizal Memorial State University - Siocon Campus. Your voice matters. Vote securely, transparently, and easily.</p>
                    
                    <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 pt-4">
                        <a href="voter_dashboard.php" class="group flex items-center justify-center px-8 py-4 text-lg font-bold rounded-xl text-primary bg-secondary hover:bg-accent transition-all shadow-[0_0_20px_rgba(218,165,32,0.4)] transform hover:-translate-y-1">
                            <span>Access Official Ballot</span><svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </a>
                        <button onclick="openRegisterModal()" class="flex items-center justify-center px-8 py-4 text-lg font-bold rounded-xl text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 transition-all">
                            Register to Vote
                        </button>
                    </div>
                </div>

                <div class="hidden lg:flex justify-center items-center relative animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="absolute w-[450px] h-[450px] rounded-full border border-white/10 animate-[spin_20s_linear_infinite]"></div>
                    <div class="absolute w-[350px] h-[350px] rounded-full border border-secondary/20 animate-[spin_15s_linear_infinite_reverse]"></div>
                    <div class="relative animate-float bg-white p-6 rounded-full border-8 border-white/10 shadow-[0_0_50px_rgba(218,165,32,0.3)] flex items-center justify-center overflow-hidden"><div class="absolute inset-0 border-4 border-secondary/20 rounded-full"></div><img src="OIP.jpg" alt="JRMSU Big Logo" class="w-64 h-64 object-contain relative z-10"></div>
                </div>
            </div>
        </div>
        
        <div class="absolute bottom-0 w-full overflow-hidden leading-none z-0">
            <svg class="relative block w-full h-[50px] md:h-[100px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118,130.42,126.38,198.5,117.8,241.28,112.42,283.47,88.75,321.39,56.44Z" class="fill-gray-50"></path></svg>
        </div>
    </main>

    <section id="features" class="py-20 lg:py-32 bg-gray-50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 lg:mb-24"><h2 class="text-secondary font-bold tracking-widest uppercase text-sm mb-2">System Capabilities</h2><h3 class="text-3xl md:text-5xl font-extrabold text-primary mb-6">Modernizing Student Democracy</h3><p class="text-gray-600 text-lg">Our custom-built platform ensures that every election is conducted with the highest standards of integrity, accuracy, and ease of use.</p></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="group bg-white p-10 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:border-secondary/30 transition-all duration-300 transform hover:-translate-y-2 text-center"><div class="w-20 h-20 mx-auto bg-blue-50 text-primary rounded-2xl flex items-center justify-center mb-8 group-hover:bg-primary group-hover:text-white transition-colors duration-300 shadow-inner"><svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg></div><h4 class="text-2xl font-bold text-gray-900 mb-4">Highly Secure</h4><p class="text-gray-600 leading-relaxed">End-to-end data encryption ensures that your choices remain entirely anonymous and immune to tampering.</p></div>
                <div class="group bg-white p-10 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:border-secondary/30 transition-all duration-300 transform hover:-translate-y-2 text-center"><div class="w-20 h-20 mx-auto bg-yellow-50 text-secondary rounded-2xl flex items-center justify-center mb-8 group-hover:bg-secondary group-hover:text-white transition-colors duration-300 shadow-inner"><svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 019.75 19.875V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg></div><h4 class="text-2xl font-bold text-gray-900 mb-4">Real-Time Tally</h4><p class="text-gray-600 leading-relaxed">The dashboard tabulates votes instantaneously. Say goodbye to manual counting and human error.</p></div>
                <div class="group bg-white p-10 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:border-secondary/30 transition-all duration-300 transform hover:-translate-y-2 text-center"><div class="w-20 h-20 mx-auto bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-green-600 group-hover:text-white transition-colors duration-300 shadow-inner"><svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg></div><h4 class="text-2xl font-bold text-gray-900 mb-4">Mobile Ready</h4><p class="text-gray-600 leading-relaxed">Fully optimized for smartphones, tablets, and desktops. Cast your vote seamlessly from anywhere on campus.</p></div>
            </div>
        </div>
    </section>

    <section id="guidelines" class="py-20 lg:py-32 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-1/2">
                    <h2 class="text-secondary font-bold tracking-widest uppercase text-sm mb-2">User Guide</h2>
                    <h3 class="text-3xl md:text-5xl font-extrabold text-primary mb-8 leading-tight">Voting is as easy as 1, 2, 3.</h3>
                    <div class="space-y-8">
                        <div class="flex items-start"><div class="flex-shrink-0 w-14 h-14 rounded-full bg-primary text-secondary flex items-center justify-center font-black text-xl shadow-lg border-2 border-secondary/20">1</div><div class="ml-6 mt-2"><h4 class="text-xl font-bold text-gray-900 mb-2">Log In Securely</h4><p class="text-gray-600">Click the "Vote Now" button and enter your official Student ID Number to access the portal.</p></div></div>
                        <div class="flex items-start"><div class="flex-shrink-0 w-14 h-14 rounded-full bg-primary text-secondary flex items-center justify-center font-black text-xl shadow-lg border-2 border-secondary/20">2</div><div class="ml-6 mt-2"><h4 class="text-xl font-bold text-gray-900 mb-2">Select Your Candidates</h4><p class="text-gray-600">Review the list of candidates for each position. Tap or click on the candidate's card to select them.</p></div></div>
                        <div class="flex items-start"><div class="flex-shrink-0 w-14 h-14 rounded-full bg-primary text-secondary flex items-center justify-center font-black text-xl shadow-lg border-2 border-secondary/20">3</div><div class="ml-6 mt-2"><h4 class="text-xl font-bold text-gray-900 mb-2">Submit Official Ballot</h4><p class="text-gray-600">Review your choices carefully and hit submit. <strong class="text-red-500">Note:</strong> Once submitted, your vote is final.</p></div></div>
                    </div>
                </div>
                <div class="lg:w-1/2 relative w-full flex justify-center">
                    <div class="absolute inset-0 bg-gradient-to-tr from-primary/10 to-secondary/10 rounded-3xl transform rotate-3 scale-105"></div>
                    <img src="j.png" alt="Students Voting" class="relative rounded-3xl shadow-2xl object-cover h-[500px] w-full border-4 border-white">
                </div>
            </div>
        </div>
    </section>

    <div id="register-modal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-[100] px-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl relative">
            <button onclick="closeRegisterModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h3 class="text-2xl font-bold text-primary mb-6">Student Registration</h3>
            <p class="text-sm text-gray-500 mb-6">Register to vote. Your application must be approved by the admin before you can cast your ballot.</p>
            <form id="register-form" onsubmit="submitRegistration(event)">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Student ID Number</label>
                    <input type="text" id="reg-student-id" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                    <input type="text" id="reg-name" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Course & Year</label>
                    <input type="text" id="reg-course" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-secondary focus:border-secondary" placeholder="e.g. BSIT - 3">
                </div>
                <button type="submit" id="reg-submit-btn" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-secondary transition-colors">Submit Registration</button>
            </form>
        </div>
    </div>

    <footer class="bg-[#001021] border-t-4 border-secondary text-gray-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
            <div class="flex items-center space-x-3 mb-8 opacity-75 grayscale hover:grayscale-0 transition-all duration-300"><div class="h-10 w-10 logo-circle-container border border-secondary/30"><img src="OIP.jpg" alt="JRMSU Logo" class="h-8 w-8 object-contain"></div><span class="text-xl font-bold text-white tracking-widest">JRMSU</span></div>
            <p class="text-sm text-center max-w-lg mx-auto mb-6">Jose Rizal Memorial State University - Siocon Campus <br>Supreme Student Government Commission on Elections (COMELEC) <br>Official Electronic Voting System</p>
            <div class="flex space-x-6 mb-8 text-xs font-bold uppercase tracking-widest"><a href="voter_dashboard.php" class="hover:text-secondary transition-colors">Voter Portal</a><span class="text-gray-600">|</span><a href="#guidelines" class="hover:text-secondary transition-colors">Help</a><span class="text-gray-600">|</span><a href="login.php" class="hover:text-secondary transition-colors">Admin Portal</a></div>
            <div class="text-xs text-center border-t border-white/10 pt-6 w-full opacity-60">&copy; <?php echo date("Y"); ?> JRMSU Siocon SSG E-Voting System. All rights reserved.</div>
        </div>
    </footer>

    <script>
        window.addEventListener('scroll', () => { const nav = document.getElementById('navbar'); if (window.scrollY > 20) { nav.classList.add('shadow-lg', 'bg-primary'); nav.classList.remove('bg-primary/90'); } else { nav.classList.remove('shadow-lg', 'bg-primary'); nav.classList.add('bg-primary/90'); } });
        const btn = document.getElementById('mobile-menu-btn'); const menu = document.getElementById('mobile-menu'); const icon = document.getElementById('menu-icon');
        btn.addEventListener('click', () => { menu.classList.toggle('hidden'); if (menu.classList.contains('hidden')) { icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16'); } else { icon.setAttribute('d', 'M6 18L18 6M6 6l12 12'); } });
        const mobileLinks = menu.querySelectorAll('a'); mobileLinks.forEach(link => { link.addEventListener('click', () => { menu.classList.add('hidden'); icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16'); }); });

        // Registration Functions
        function openRegisterModal() {
            document.getElementById('register-modal').classList.remove('hidden');
            document.getElementById('register-modal').classList.add('flex');
        }
        function closeRegisterModal() {
            document.getElementById('register-modal').classList.add('hidden');
            document.getElementById('register-modal').classList.remove('flex');
        }

        async function submitRegistration(e) {
            e.preventDefault();
            const btn = document.getElementById('reg-submit-btn');
            btn.textContent = 'Submitting...'; btn.disabled = true;
            
            const studentId = document.getElementById('reg-student-id').value;
            const name = document.getElementById('reg-name').value;
            const course = document.getElementById('reg-course').value;

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'register_voter', studentId, name, course })
                });
                const result = await response.json();
                
                if(result.success) {
                    alert('Registration successful! Please wait for the admin to approve your registration before you log in.');
                    closeRegisterModal();
                    e.target.reset();
                } else {
                    alert('Registration failed: ' + result.message);
                }
            } catch (err) {
                alert('Connection error. Please try again later.');
            }
            btn.textContent = 'Submit Registration'; btn.disabled = false;
        }
    </script>
</body>
</html>
