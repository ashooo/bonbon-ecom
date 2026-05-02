@extends('layouts.app')

@section('content')
    <section class="mb-10">
        <div class="rounded-[2rem] border border-[#F3D7DD] bg-gradient-to-br from-white via-[#FFF8F9] to-[#FDF0F3] p-8 shadow-[0_20px_45px_rgba(90,58,58,0.12)] md:p-12">
            <p class="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-pink-600">Cake Customization</p>
            <h1 class="text-4xl font-bold text-[#5A3A3A] md:text-5xl">Build Your Dream Cake</h1>
            <p class="mt-4 max-w-3xl text-base leading-relaxed text-[#7A5252] md:text-lg">
                Choose the cake base, flavors, design style, service options, and dietary preferences in one place.
                This form is structured so customers can clearly describe exactly what they want.
            </p>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-10 xl:grid-cols-[1.15fr_0.85fr]">
        <form class="space-y-8">
            <section class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg">
                <h2 class="mb-5 text-2xl font-bold text-[#5A3A3A]">Cake Base & Structure</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Sponge Type</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>Chocolate</option>
                            <option>Vanilla</option>
                            <option>Red Velvet</option>
                            <option>Ube</option>
                            <option>Strawberry</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Filling</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>Ganache</option>
                            <option>Custard</option>
                            <option>Fruit Jam</option>
                            <option>Cream Cheese</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Frosting Type</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>Buttercream</option>
                            <option>Whipped Cream</option>
                            <option>Fondant</option>
                            <option>Ganache</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Layers</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>1 Layer</option>
                            <option>2 Layers</option>
                            <option>3 Layers</option>
                            <option>4 Layers</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Shape</label>
                        <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
                            <button type="button" class="rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-sm font-medium text-[#482B31] hover:bg-[#FDECEF]">Round</button>
                            <button type="button" class="rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-sm font-medium text-[#482B31] hover:bg-[#FDECEF]">Square</button>
                            <button type="button" class="rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-sm font-medium text-[#482B31] hover:bg-[#FDECEF]">Heart</button>
                            <button type="button" class="rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-sm font-medium text-[#482B31] hover:bg-[#FDECEF]">Number Cake</button>
                            <button type="button" class="rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-sm font-medium text-[#482B31] hover:bg-[#FDECEF]">Custom</button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg">
                <h2 class="mb-5 text-2xl font-bold text-[#5A3A3A]">Flavor Enhancements</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Syrup / Soak Flavor</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>Coffee</option>
                            <option>Milk</option>
                            <option>Fruit Syrup</option>
                            <option>Vanilla Syrup</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Mix-ins</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>None</option>
                            <option>Nuts</option>
                            <option>Chocolate Chips</option>
                            <option>Fruit Bits</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg">
                <h2 class="mb-5 text-2xl font-bold text-[#5A3A3A]">Occasion Details</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Occasion Type</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>Birthday</option>
                            <option>Anniversary</option>
                            <option>Graduation</option>
                            <option>Wedding</option>
                            <option>Baby Shower</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Age / Milestone Number</label>
                        <input type="text" placeholder="e.g. 18, 25, 50" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Message on Cake</label>
                        <textarea rows="3" placeholder="Write the message that should appear on the cake" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300"></textarea>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg">
                <h2 class="mb-5 text-2xl font-bold text-[#5A3A3A]">Add-ons</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Candles</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>None</option>
                            <option>Number Candles</option>
                            <option>Sparkler Candles</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Cake Topper</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>None</option>
                            <option>Name Topper</option>
                            <option>Acrylic Topper</option>
                            <option>Edible Print</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Figurines / Decorations</label>
                        <input type="text" placeholder="e.g. flowers, cartoon toppers" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg">
                <h2 class="mb-5 text-2xl font-bold text-[#5A3A3A]">Design & Style</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Color Palette</label>
                        <input type="text" placeholder="e.g. pink, cream, gold" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Theme</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>Minimalist</option>
                            <option>Elegant</option>
                            <option>Cartoon</option>
                            <option>Floral</option>
                            <option>Luxury</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Finish Style</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>Smooth</option>
                            <option>Textured</option>
                            <option>Drip Cake</option>
                            <option>Watercolor Effect</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Edible Print Image Upload</label>
                        <input type="file" accept="image/*" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg">
                <h2 class="mb-5 text-2xl font-bold text-[#5A3A3A]">Service Options</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Delivery or Pickup</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>Delivery</option>
                            <option>Pickup</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Delivery Date</label>
                        <input type="date" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Delivery Time</label>
                        <input type="time" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Size in Servings</label>
                        <select class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-[#482B31] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option>6" / 6-8 people</option>
                            <option>8" / 10-12 people</option>
                            <option>10" / 14-16 people</option>
                            <option>12" / 20+ people</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-3 rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-4 text-[#482B31]">
                            <input type="checkbox" class="h-5 w-5 rounded border-[#F3D7DB] text-pink-500 focus:ring-pink-300">
                            <span class="font-medium">Rush Order Option</span>
                        </label>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg">
                <h2 class="mb-5 text-2xl font-bold text-[#5A3A3A]">Dietary Preferences</h2>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <label class="flex items-center gap-3 rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-4 text-[#482B31]">
                        <input type="checkbox" class="h-5 w-5 rounded border-[#F3D7DB] text-pink-500 focus:ring-pink-300">
                        <span>Eggless</span>
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-4 text-[#482B31]">
                        <input type="checkbox" class="h-5 w-5 rounded border-[#F3D7DB] text-pink-500 focus:ring-pink-300">
                        <span>Sugar-free</span>
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-4 text-[#482B31]">
                        <input type="checkbox" class="h-5 w-5 rounded border-[#F3D7DB] text-pink-500 focus:ring-pink-300">
                        <span>Gluten-free</span>
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-4 text-[#482B31]">
                        <input type="checkbox" class="h-5 w-5 rounded border-[#F3D7DB] text-pink-500 focus:ring-pink-300">
                        <span>Vegan</span>
                    </label>
                </div>
            </section>
        </form>

        <aside class="space-y-6">
            <section class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-xl font-bold text-pink-600">Cake Preview</h2>
                <div class="overflow-hidden rounded-3xl border border-[#F3D7DB] bg-gradient-to-b from-pink-50 to-pink-100 p-6 shadow-inner">
                    <div class="relative h-96 overflow-hidden rounded-3xl bg-gradient-to-b from-[#FFF8FA] to-[#FADBE2]">
                        <div class="absolute bottom-0 left-1/2 h-36 w-72 -translate-x-1/2 rounded-t-[999px] bg-white shadow-lg"></div>
                        <div class="absolute bottom-16 left-1/2 h-20 w-80 -translate-x-1/2 rounded-t-[999px] bg-[#EAB7C1] shadow-md"></div>
                        <div class="absolute inset-x-0 top-10 text-center">
                            <p class="font-script text-4xl text-[#7A3444]">Happy Birthday</p>
                            <p class="mt-3 text-sm uppercase tracking-[0.25em] text-[#A06B76]">Elegant Floral Theme</p>
                        </div>
                    </div>
                </div>
                <p class="mt-4 text-sm text-[#7A4F57]">
                    Preview your cake concept here. This layout now reflects the real customization categories instead of the old text-only editor.
                </p>
            </section>

            <section class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg">
                <h2 class="mb-4 text-xl font-bold text-[#5A3A3A]">Order Summary</h2>
                <div class="space-y-3 text-sm text-[#6E4D53]">
                    <div class="flex justify-between">
                        <span>Base Cake</span>
                        <span>Chocolate / 2 Layers</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Design Theme</span>
                        <span>Elegant Floral</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Service</span>
                        <span>Delivery</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Size</span>
                        <span>8" / 10-12 people</span>
                    </div>
                </div>
                <div class="mt-5 border-t border-[#F3D7DD] pt-5">
                    <div class="flex items-center justify-between text-lg font-bold text-[#5A3A3A]">
                        <span>Estimated Total</span>
                        <span>&#8369;2,450.00</span>
                    </div>
                </div>
                <button class="mt-6 w-full rounded-2xl bg-pink-600 px-6 py-4 text-lg font-bold text-white transition duration-300 hover:bg-pink-700">
                    Submit Cake Request
                </button>
            </section>
        </aside>
    </div>
@endsection
