<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Panel'; ?> - JackoTimespiece</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg-gold { background-color: #c9b37e; }
        .text-gold { color: #c9b37e; }
        .border-gold { border-color: #c9b37e; }
        .hover\:bg-gold:hover { background-color: #c9b37e; }
        .focus\:border-gold:focus { border-color: #c9b37e; }
        .focus\:ring-gold:focus { --tw-ring-color: #c9b37e; }
    </style>
</head>
<body class="bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-black border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="w-8 h-8 bg-gold rounded-md flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-black"></i>
                        </div>
                        <span class="text-white font-bold text-xl">JackoTimespiece</span>
                        <span class="text-gray-400 ml-2">Admin</span>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <?php
                    $nav = getAdminNavigation();
                    foreach ($nav as $key => $item):
                    ?>
                    <a href="<?php echo $item['url']; ?>" 
                       class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition
                              <?php echo isActiveAdminPage($key . '.php') ? 'bg-gray-800 text-white' : ''; ?>">
                        <i class="<?php echo $item['icon']; ?> mr-2"></i>
                        <?php echo $item['title']; ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button class="text-gray-300 hover:text-white p-2 rounded-md transition">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                3
                            </span>
                        </button>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center text-gray-300 hover:text-white transition">
                            <img class="h-8 w-8 rounded-full object-cover" 
                                 src="../assets/images/users/<?php echo htmlspecialchars($_SESSION['user']['image']); ?>" 
                                 alt="Profile">
                            <span class="ml-2 text-sm font-medium"><?php echo htmlspecialchars($_SESSION['user']['first_name']); ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-md shadow-lg py-1 z-50">
                            <a href="../public/account/settings.php" 
                               class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                <i class="fas fa-user mr-2"></i>
                                Profile Settings
                            </a>
                            <a href="../public/index.php" 
                               class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                <i class="fas fa-store mr-2"></i>
                                View Store
                            </a>
                            <hr class="border-gray-700 my-1">
                            <a href="../public/logout.php" 
                               class="block px-4 py-2 text-sm text-red-400 hover:bg-gray-700 hover:text-red-300 transition">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Sign Out
                            </a>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                                class="text-gray-300 hover:text-white p-2 rounded-md transition">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="md:hidden bg-gray-800 border-t border-gray-700">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <?php foreach ($nav as $key => $item): ?>
                <a href="<?php echo $item['url']; ?>" 
                   class="text-gray-300 hover:text-white block px-3 py-2 rounded-md text-base font-medium transition
                          <?php echo isActiveAdminPage($key . '.php') ? 'bg-gray-700 text-white' : ''; ?>">
                    <i class="<?php echo $item['icon']; ?> mr-2"></i>
                    <?php echo $item['title']; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-white"><?php echo $pageTitle ?? 'Admin Panel'; ?></h1>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <div>
                                    <a href="index.php" class="text-gray-400 hover:text-white transition">
                                        <i class="fas fa-home"></i>
                                        <span class="sr-only">Home</span>
                                    </a>
                                </div>
                            </li>
                            <?php if (isset($breadcrumbs)): ?>
                                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                                <li>
                                    <div class="flex items-center">
                                        <i class="fas fa-chevron-right text-gray-500 mx-2"></i>
                                        <?php if (isset($breadcrumb['url'])): ?>
                                            <a href="<?php echo $breadcrumb['url']; ?>" 
                                               class="text-gray-400 hover:text-white transition">
                                                <?php echo htmlspecialchars($breadcrumb['title']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-white"><?php echo htmlspecialchars($breadcrumb['title']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ol>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (isset($pageActions)): ?>
                        <?php foreach ($pageActions as $action): ?>
                        <a href="<?php echo $action['url']; ?>" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-black bg-gold hover:bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold transition">
                            <i class="<?php echo $action['icon']; ?> mr-2"></i>
                            <?php echo $action['title']; ?>
                        </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js for dropdowns -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html> 