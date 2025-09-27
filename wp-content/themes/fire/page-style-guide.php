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
        <div class="flex">
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-primary-100">primary-100</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-primary-200">primary-200</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-primary-300">primary-300</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-primary-500">primary-500</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-primary-600">primary-600</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-primary-700">primary-700</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-primary-800">primary-800</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-primary-900">primary-900</div>
        </div>
        <div class="flex">
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-secondary-100">secondary-100</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-secondary-200">secondary-200</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-secondary-300">secondary-300</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-secondary-500">secondary-500</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-secondary-600">secondary-600</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-secondary-700">secondary-700</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-secondary-800">secondary-800</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-secondary-900">secondary-900</div>
        </div>
        <div class="flex">
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-accent-100">accent-100</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-accent-200">accent-200</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-accent-300">accent-300</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-accent-500">accent-500</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-accent-600">accent-600</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-accent-700">accent-700</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-accent-800">accent-800</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-accent-900">accent-900</div>
        </div>
        <div class="flex">
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-gray-100">gray-100</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-gray-200">gray-200</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-black bg-gray-300">gray-300</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-gray-500">gray-500</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-gray-600">gray-600</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-gray-700">gray-700</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-gray-800">gray-800</div>
          <div class="flex items-center justify-center w-32 h-20 mb-4 mr-4 text-white bg-gray-900">gray-900</div>
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

      <p>paragraph: Lorem ipsum dolor sit amet consectetur, adipisicing elit. Reprehenderit, voluptatibus perferendis recusandae error unde repudiandae. Iste ab eius quibusdam inventore?</p>

      <hr>

      <div class="flex flex-wrap">
        <button type="button" class="button button-primary">primary</button>
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