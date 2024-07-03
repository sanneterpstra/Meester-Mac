<div class="bg-gray-50 py-18">
	<div
			class="mx-auto max-w-7xl px-4 sm:px-8"
			x-data="{
            container: $refs.carrousel,
            containerWidth: 0,
            containerInnerWidth: 0,
            scrollToPosition: 0,
            init() {
                this.containerWidth = this.container.offsetWidth;
                this.containerInnerWidth = this.container.scrollWidth
            },
            scrollToRight() {
                if (this.scrollToPosition < this.containerInnerWidth - this.containerWidth) {
                    this.scrollToPosition += this.containerWidth;
                    this.container.scrollTo({ left: this.scrollToPosition, behavior: 'smooth' });
                }
            },
            scrollToLeft() {
                if (this.scrollToPosition > 10) {
                    this.scrollToPosition -= this.containerWidth;
                    this.container.scrollTo({ left: this.scrollToPosition, behavior: 'smooth' });
                }
            }
        }"
			x-on:resize.window.throttle="containerWidth = container.offsetWidth"
	>
		<div class="text-center mb-10">
			<h2 class="text-2xl lg:text-3xl font-bold">Reviews</h2>

			<p class="text-lg lg:text-xl mt-3"><a class="text-blue-500 underline"
												  href="https://www.google.com/search?q=meester+mac">
					83 reviews</a>
				geven Meester Mac gemiddeld 5 van de 5 sterren.</p>
		</div>

		<div
				class="snap-x snap-mandatory flex flex-nowrap overflow-x-scroll max-h-[50rem] space-x-8 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
				x-ref="carrousel"
				x-on:scroll.throttle="scrollToPosition = container.scrollLeft"
		>

			@foreach ($reviews as $review)
				<div class="snap-start flex-none h-[400px] flex flex-col md:w-96 w-full bg-white p-6">
					<div class="h-[296px] overflow-y-scroll [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
						<p>{{ $review->comment }}</p>
					</div>
					<div class="flex items-center space-x-4 mt-12">
						<img
								src="{{ $review->profilePhotoUrl }}"
								class="flex-none w-14 h-14 rounded-full object-cover"
						>
						<div class="flex-auto">
							<div class="text-base">
								{{ $review->displayName }}
							</div>
							<div class="">
								<div class="flex items-center">
									@for ($k = 0; $k < $review->starRating; $k++)
										<svg
												class="text-yellow-400 h-4 w-4 flex-shrink-0"
												viewBox="0 0 20 20"
												fill="currentColor"
												aria-hidden="true"
										>
											<path
													fill-rule="evenodd"
													d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
													clip-rule="evenodd"
											/>
										</svg>
									@endfor
								</div>
							</div>
						</div>
					</div>
				</div>
			@endforeach

		</div>
		<div class="text-center mt-10">
			<button
					@click="scrollToLeft()"
					class="rounded-full p-3 border-2 border-gray-500 hover:border-gray-900"
			>
				<svg
						xmlns="http://www.w3.org/2000/svg"
						fill="none"
						viewBox="0 0 24 24"
						stroke-width="1.5"
						stroke="currentColor"
						class="w-6 h-6"
				>
					<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
					/>
				</svg>
			</button>
			<button
					@click="scrollToRight()"
					class="rounded-full p-3 border-2 border-gray-500 hover:border-gray-900"
			>
				<svg
						xmlns="http://www.w3.org/2000/svg"
						fill="none"
						viewBox="0 0 24 24"
						stroke-width="1.5"
						stroke="currentColor"
						class="w-6 h-6"
				>
					<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
					/>
				</svg>
			</button>
		</div>
	</div>
</div>
