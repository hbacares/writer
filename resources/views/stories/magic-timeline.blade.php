<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('stories.show', $story) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Magic Aspects Timeline
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div x-data="magicTimeline()" class="space-y-6">
                        {{-- Year Slider --}}
                        <div class="text-center space-y-4">
                            <div class="flex items-center justify-center gap-4">
                                <label for="yearSlider" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Year:
                                </label>
                                <input
                                    id="yearSlider"
                                    type="range"
                                    min="0"
                                    max="200"
                                    x-model.number="year"
                                    class="flex-1 max-w-md h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                />
                                <span class="text-2xl font-bold text-gray-900 dark:text-gray-100 min-w-[80px]" x-text="year + 938"></span>
                            </div>
                        </div>

                        {{-- Magic Circle Visualization --}}
                        <div class="flex justify-center">
                            <svg width="400" height="400" viewBox="0 0 400 400" class="max-w-full h-auto">
                                <g :transform="'rotate(' + rotation + ' 200 200)'">
                                    @for ($i = 0; $i < 10; $i++)
                                        <path
                                            :d="createSlice({{ $i }})"
                                            :fill="getSliceColor({{ $i }})"
                                            stroke="black"
                                            stroke-width="1"
                                        />
                                        <text
                                            :x="getTextX({{ $i }})"
                                            :y="getTextY({{ $i }})"
                                            font-size="12"
                                            font-weight="bold"
                                            text-anchor="middle"
                                            alignment-baseline="middle"
                                            fill="black"
                                            :transform="'rotate(' + (-rotation) + ' ' + getTextX({{ $i }}) + ' ' + getTextY({{ $i }}) + ')'"
                                            x-text="getLabel({{ $i }})"
                                        ></text>
                                    @endfor
                                </g>
                                {{-- Horizontal reference line --}}
                                <line x1="20" y1="200" x2="380" y2="200" stroke="#ffffff99" stroke-width="2" />
                            </svg>
                        </div>

                        {{-- Info Text --}}
                        <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                            <p>The Magic Aspects Timeline shows which magical aspects are dominant in different years.</p>
                            <p class="mt-2">Aspects above the horizontal line are active in that year.</p>
                            <template x-if="year > 80">
                                <p class="mt-2 font-semibold text-emerald-600 dark:text-emerald-400">
                                    Special aspects unlocked: Glass, Necro, and Ash
                                </p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function magicTimeline() {
            return {
                year: 0,
                aspects: [
                    { name: "Sand", color: "#A0522D" },
                    { name: "Wind", color: "#A9DFEF" },
                    { name: "Jade", color: "#00A86B" },
                    { name: "Water", color: "#1E90FF" },
                    { name: "Fire", color: "#FF4500" },
                    { name: "Light", color: "#E0FFFF" },
                    { name: "Wood", color: "#8B4513" },
                    { name: "Diamond", color: "#DDA0DD" },
                    { name: "Silk", color: "#A9A9A9" },
                    { name: "Stone", color: "#B0C4DE" }
                ],

                get rotation() {
                    return this.year * (360 / 200);
                },

                createSlice(index) {
                    const anglePerSlice = 360 / this.aspects.length;
                    const startAngle = 180 - index * anglePerSlice;
                    const endAngle = 180 - (index + 1) * anglePerSlice;
                    const largeArc = Math.abs(endAngle - startAngle) > 180 ? 1 : 0;

                    const start = this.polarToCartesian(200, 200, 180, startAngle);
                    const end = this.polarToCartesian(200, 200, 180, endAngle);

                    return [
                        `M 200 200`,
                        `L ${start.x} ${start.y}`,
                        `A 180 180 0 ${largeArc} 0 ${end.x} ${end.y}`,
                        `Z`
                    ].join(' ');
                },

                getLabel(index) {
                    if (this.year > 80 && index === 5) {
                        return "Glass";
                    }
                    if (this.year > 80 && index === 7) {
                        return "Necro";
                    }
                    if (this.year > 80 && index === 8) {
                        return "Ash";
                    }
                    return this.aspects[index].name;
                },

                polarToCartesian(cx, cy, r, angleInDegrees) {
                    const angleInRadians = angleInDegrees * Math.PI / 180;
                    return {
                        x: cx + r * Math.cos(angleInRadians),
                        y: cy + r * Math.sin(angleInRadians)
                    };
                },

                getSliceColor(index) {
                    const anglePerSlice = 360 / this.aspects.length;
                    const rawStartAngle = 180 - (index - 2.5) * anglePerSlice;
                    const rawEndAngle = 180 - (index - 1.6) * anglePerSlice;

                    const rotatedStart = (rawStartAngle + this.rotation + 360) % 360;
                    const rotatedEnd = (rawEndAngle + this.rotation + 360) % 360;

                    const inTop = angle => angle <= 90 || angle >= 270;

                    // Color if either edge is still in top half
                    if (inTop(rotatedStart) || inTop(rotatedEnd)) {
                        return this.aspects[index].color;
                    }

                    return "#000000";
                },

                getTextX(index) {
                    const anglePerSlice = 360 / this.aspects.length;
                    const angle = 180 - (index + 0.5) * anglePerSlice;
                    return 200 + 130 * Math.cos(angle * Math.PI / 180);
                },

                getTextY(index) {
                    const anglePerSlice = 360 / this.aspects.length;
                    const angle = 180 - (index + 0.5) * anglePerSlice;
                    return 200 + 130 * Math.sin(angle * Math.PI / 180);
                }
            }
        }
    </script>
</x-app-layout>
