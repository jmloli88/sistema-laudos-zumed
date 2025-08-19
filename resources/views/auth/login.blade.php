<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            overflow-x: hidden;
        }
        
        .blur-effect {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        .login-container {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .login-image {
            background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2940');
            background-size: cover;
            background-position: center;
        }
        
        .form-input:focus {
            transition: all 0.3s ease;
            transform: translateY(-2px);
        }
        
        .login-button {
            transition: all 0.3s ease;
        }
        
        .login-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 124, 255, 0.2);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.6s ease forwards;
        }
        
        .delay-100 {
            animation-delay: 0.1s;
        }
        
        .delay-200 {
            animation-delay: 0.2s;
        }
        
        .delay-300 {
            animation-delay: 0.3s;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="login-container w-full max-w-5xl bg-white rounded-2xl flex overflow-hidden">
        <!-- Lado izquierdo - Imagen -->
        <div class="login-image hidden md:block md:w-1/2 relative">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/40 to-indigo-600/40"></div>
            <div class="absolute bottom-0 left-0 right-0 p-10 blur-effect bg-white/10 rounded-tr-3xl">
                <h2 class="text-white text-3xl font-bold mb-2">Sistema de Laudos ZuMed</h2>
                <p class="text-white/80">Gerencie seus laudos de forma eficiente e segura</p>
            </div>
        </div>
        
        <!-- Lado derecho - Formulario -->
        <div class="w-full md:w-1/2 p-8 md:p-12">
            <div class="animate-fadeIn opacity-0">
                <div class="flex justify-center mb-8">
                    <div class="h-14 w-14 rounded-full bg-blue-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Bem-vindo</h2>
                    <p class="text-gray-500 mt-2">Faça login para continuar</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                @if (session('message'))
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg animate-fadeIn opacity-0">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="animate-fadeIn opacity-0 delay-100">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Usuário</label>
                    <input
                        type="text"
                        name="username"
                        class="form-input w-full px-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition duration-300"
                        required
                        autocomplete="username"
                    >
                </div>

                <div class="animate-fadeIn opacity-0 delay-200">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Senha</label>
                    <input
                        type="password"
                        name="password"
                        class="form-input w-full px-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition duration-300"
                        required
                        autocomplete="current-password"
                    >
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg animate-fadeIn opacity-0">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="animate-fadeIn opacity-0 delay-300">
                    <button
                        type="submit"
                        class="login-button w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3.5 rounded-xl hover:from-blue-600 hover:to-indigo-700 font-medium text-sm shadow-lg"
                    >
                        Entrar
                    </button>
                </div>
                
                <div class="animate-fadeIn opacity-0 delay-300 text-center mt-6">
                    <p class="text-sm text-gray-500">Problemas para acessar? Entre em contato com o suporte técnico</p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
