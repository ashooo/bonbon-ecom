<footer class="bg-[#F5F5F5] text-[#2E2E2E] py-8 border-t border-[#E6B7BE]">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                <p>Email: {{ $storeSettings?->footer_email ?: 'exceeed12@gmail.com' }}</p>
                <p>Phone: {{ $storeSettings?->footer_phone ?: '0917 357 7557' }}</p>
                <p>Address: {{ $storeSettings?->footer_address ?: 'Paranaque, Philippines' }}</p>
                @if($storeSettings?->footer_hours)
                    <div class="mt-4 pt-4 border-t border-[#E6B7BE]">
                        <p class="font-semibold mb-2">Store Hours:</p>
                        <p class="text-sm whitespace-pre-line">{{ $storeSettings->footer_hours }}</p>
                    </div>
                @endif
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                <div class="flex space-x-4">
                    <a href="https://www.facebook.com/BonbonsPHofficial" class="text-[#5A3A3A] hover:text-[#E6B7BE]">Facebook</a>
                    <a href="#" class="text-[#5A3A3A] hover:text-[#E6B7BE]">Instagram</a>
                    <a href="#" class="text-[#5A3A3A] hover:text-[#E6B7BE]">Messenger</a>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="/" class="text-[#5A3A3A] hover:text-[#E6B7BE]">Home</a></li>
                    <li><a href="/products" class="text-[#5A3A3A] hover:text-[#E6B7BE]">Products</a></li>
                    <li><a href="/customize" class="text-[#5A3A3A] hover:text-[#E6B7BE]">Customize</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-[#E6B7BE] mt-8 pt-8 text-center text-[#5A3A3A]">
            <p>{{ $storeSettings?->copyright_text ?: '© 2024 Bonbon Ecom. All rights reserved.' }}</p>
        </div>
    </div>
</footer>
