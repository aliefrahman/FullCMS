</main>

<!-- Premium Footer -->
<footer class="bg-slate-900 text-slate-400 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            <!-- Branding and Description -->
            <div class="space-y-4">
                <a href="<?php echo PUBLIC_URL; ?>/" class="flex items-center gap-2">
                    <div
                        class="h-9 w-9 rounded-lg bg-linear-to-tr from-primary-600 to-accent-500 flex items-center justify-center text-white font-display font-extrabold text-lg">
                        F
                    </div>
                    <span class="font-display font-extrabold text-xl tracking-tight text-white">
                        Full<span class="text-primary-500">CMS</span>
                    </span>
                </a>
                <p class="text-sm leading-relaxed text-slate-400">
                    Create outstanding modern web portals, blogs, and corporate sites with our next-generation PHP-based
                    MVC Content Management System.
                </p>
                <div class="flex items-center gap-4 pt-2">
                    <a href="#"
                        class="h-8 w-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-primary-600 hover:text-white transition-all duration-200">
                        <i data-lucide="twitter" class="w-4 h-4"></i>
                    </a>
                    <a href="#"
                        class="h-8 w-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-primary-600 hover:text-white transition-all duration-200">
                        <i data-lucide="github" class="w-4 h-4"></i>
                    </a>
                    <a href="#"
                        class="h-8 w-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-primary-600 hover:text-white transition-all duration-200">
                        <i data-lucide="linkedin" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Navigation Categorized Links -->
            <div>
                <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Features</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Layout Builder</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">SEO Optimizer</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Speed Optimization</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Dynamic Widgets</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Company</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors duration-200">About Us</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Our Blog</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Careers</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Contact Us</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Resources</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Documentation</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Help Center</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Terms of Use</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Footer Area -->
        <div
            class="mt-12 pt-8 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
            <p>&copy; <?php echo date('Y'); ?> FullCMS. Built for absolute speed and performance.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
<script src="<?php echo PUBLIC_URL; ?>/assets/js/lucide.min.js"></script>
<script src="<?php echo PUBLIC_URL; ?>/assets/quill/dist/quill.js"></script>
<script>
    lucide.createIcons();
</script>
</body>

</html>