<?php
  /* Template Name: Style Guide */
  /**
   * The template for displaying the Style Guide
   *
   * @package Fire
   */

  get_header();
?>
	<main id="primary" class="site-main">
    <div class="fire-container mx-auto mt-10 mb-20">
      <div>
        <h2 class="mb-4 text-2xl font-bold">Theme Colors</h2>
        <div class="flex flex-wrap gap-4">
          <div class="flex flex-col items-center">
            <div class="flex items-center justify-center w-40 h-24 mb-2 text-white bg-light-blue">
              <span class="text-sm font-semibold">Light Blue</span>
            </div>
            <span class="text-xs text-gray-600">#64bfdd</span>
          </div>
          <div class="flex flex-col items-center">
            <div class="flex items-center justify-center w-40 h-24 mb-2 text-white bg-blue">
              <span class="text-sm font-semibold">Blue</span>
            </div>
            <span class="text-xs text-gray-600">#325087</span>
          </div>
          <div class="flex flex-col items-center">
            <div class="flex items-center justify-center w-40 h-24 mb-2 text-white bg-navy">
              <span class="text-sm font-semibold">Navy</span>
            </div>
            <span class="text-xs text-gray-600">#223f5d</span>
          </div>
          <div class="flex flex-col items-center">
            <div class="flex items-center justify-center w-40 h-24 mb-2 text-white bg-teal">
              <span class="text-sm font-semibold">Teal</span>
            </div>
            <span class="text-xs text-gray-600">#5eaf98</span>
          </div>
          <div class="flex flex-col items-center">
            <div class="flex items-center justify-center w-40 h-24 mb-2 text-white bg-charcoal">
              <span class="text-sm font-semibold">Charcoal</span>
            </div>
            <span class="text-xs text-gray-600">#282828</span>
          </div>
        </div>
      </div>

      <hr>

      <h1>h1: Lorem ipsum dolor sit amet consectetur adipisicing elit.</h1>
      <h2>h2: Lorem ipsum dolor sit amet consectetur adipisicing elit.</h2>
      <h3>h3: Lorem ipsum dolor sit amet consectetur adipisicing elit.</h3>
      <h4>h4: Lorem ipsum dolor sit amet consectetur adipisicing elit.</h4>
      <h5>h5: Lorem ipsum dolor sit amet consectetur adipisicing elit.</h5>
      <h6>h6: Lorem ipsum dolor sit amet consectetur adipisicing elit.</h6>

      <h1 class="heading-1">heading-1: Lorem ipsum dolor sit amet</h1>
      <h1 class="heading-2">heading-2: Lorem ipsum dolor sit amet</h1>
      <h1 class="heading-3">heading-3: Lorem ipsum dolor sit amet</h1>
      <h1 class="heading-4">heading-4: Lorem ipsum dolor sit amet</h1>
      <h1 class="heading-5">heading-5: Lorem ipsum dolor sit amet</h1>
      <h1 class="heading-6">heading-6: Lorem ipsum dolor sit amet</h1>

      <hr>

      <p class="text-lg">text-lg paragraph: Lorem ipsum dolor sit amet consectetur, adipisicing elit. Reprehenderit, voluptatibus perferendis recusandae error unde repudiandae. Iste ab eius quibusdam inventore?</p>

      <p class="text-base">text-base paragraph: Lorem ipsum dolor sit amet consectetur, adipisicing elit. Reprehenderit, voluptatibus perferendis recusandae error unde repudiandae. Iste ab eius quibusdam inventore?</p>

      <p class="text-sm">text-sm paragraph: Lorem ipsum dolor sit amet consectetur, adipisicing elit. Reprehenderit, voluptatibus perferendis recusandae error unde repudiandae. Iste ab eius quibusdam inventore?</p>

      <p>paragraph: Lorem ipsum dolor sit amet consectetur, adipisicing elit. Reprehenderit, voluptatibus perferendis recusandae error unde repudiandae. Iste ab eius quibusdam inventore?</p>

      <hr>

      <div class="flex flex-wrap gap-4">
        <button type="button" class="button">primary</button>
        <button type="button" class="button">primary <?php new Fire_SVG('icon--chevron-right'); ?></button>
        <button type="button" class="button-outline">outline</button>
        <button type="button" class="button-charcoal">charcoal</button>
      </div>

      <hr>

      <div class="flex flex-wrap">
        <button type="button" class="mr-3 button button-transparent">button-transparent</button>
      </div>

      <hr>

      <div class="flex">
        <a href="#" class="link link-primary">primary</a>
      </div>

      <hr>

      <div class="w-1/2">
        <div class="flex">
          <div class="flex-grow mr-3 form-group">
            <label class="form-input-label">First Name</label>
            <input type="text" class="form-input">
          </div>
          <div class="flex-grow form-group">
            <label class="form-input-label">Last Name</label>
            <input type="text" class="form-input">
          </div>
        </div>
        <div class="form-group">
          <label class="form-input-label">Email Address</label>
          <input type="text" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-input-label">Comment</label>
          <textarea type="text" class="form-input"></textarea>
        </div>
      </div>
    </div>
  </main>

<?php
  get_footer();
?>