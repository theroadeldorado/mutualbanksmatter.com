<?php
/**
 * Login Section Template
 *
 * Full-width section with media background (image or Vimeo video),
 * login form with password reset functionality, and content.
 *
 * @package Fire
 */

// Retrieve ACF fields
$media_type = get_sub_field('media_type');
$image_id = get_sub_field('image');
$vimeo_video_id = get_sub_field('vimeo_video_id');
$copy = get_sub_field('copy');
$sign_up_link = get_sub_field('sign_up_link');

// Get theme logo path
$logo_path = get_template_directory_uri() . '/theme/assets/media/images/logo.png';

// Add section classes
$section->add_classes([
  'login-section py-16 lg:py-24'
]);
?>

<?php $section->start(); ?>

<div class="fire-container mb-20">
  <div class="col-[main] flex justify-center">
    <img
      src="<?php echo esc_url($logo_path); ?>"
      alt="Mutual Banks Matter"
      class="h-24 lg:h-32 xl:h-40 w-auto"
    />
  </div>
</div>

<div class="grid-stack" x-data="{ showReset: false }">
  <!-- Login Form -->
  <div class="fire-container duration-300 ease-in-out transition-all" :class="{ 'opacity-0 pointer-events-none': showReset }" x-transition>
    <div class="col-[main] md:col-[col-1/col-6] lg:col-[col-1/col-4] content-center">
      <?php if ($media_type === 'video' && $vimeo_video_id): ?>
      <div class="relative w-full rounded-lg overflow-hidden shadow-lg aspect-video bg-black">
        <iframe
          src="https://player.vimeo.com/video/<?php echo esc_attr($vimeo_video_id); ?>?autoplay=1&loop=1&autopause=0&muted=1&background=1"
          class="absolute inset-0 w-full h-full"
          frameborder="0"
          allow="autoplay; fullscreen; picture-in-picture"
          allowfullscreen
          title="Vimeo Video"
        ></iframe>
      </div>
    <?php elseif ($media_type === 'image' && $image_id): ?>
      <div class="rounded-lg overflow-hidden shadow-lg">
        <?php echo ResponsivePics::get_picture($image_id, 'sm:600 338|f, md:450 338|f, lg:600 338|f, xl:656 369|f', 'lazyload-effect', true, true); ?>
      </div>
    <?php endif; ?>
    </div>

    <div class="col-[main] md:col-[col-7/col-12] lg:col-[col-5/col-8] content-center">
      <?php if ($copy): ?>
        <div class="col-[main] text-left space-y-6">
          <div class="wizzy text-base text-white">
            <?php echo $copy; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-[main] md:col-[col-5/col-8] lg:col-[col-9/col-12] content-center">
      <form method="post" action="<?php echo esc_url(wp_login_url()); ?>" class="login-form space-y-6">
        <div class="form-field mb-6">
          <label for="user_login" class="block text-base text-white">Email</label>
          <input
            type="text"
            name="log"
            id="user_login"
            placeholder="Email"
            class="w-full bg-transparent border-0 border-b-2 border-white py-2 px-0 text-lg text-white placeholder:text-gray-400 focus:outline-none focus:shadow-none focus:border-b-light-blue"
            value=""
            size="20"
            required
          />
        </div>

        <div class="form-field mb-6">
          <label for="user_pass" class="block text-base text-white">Password</label>
          <input
            type="password"
            name="pwd"
            id="user_pass"
            placeholder="Password"
            class="w-full bg-transparent border-0 border-b-2 border-white py-2 px-0 text-lg text-white placeholder:text-gray-400 focus:outline-none focus:shadow-none focus:border-b-light-blue"
            value=""
            size="20"
            required
          />
        </div>

        <div class="form-field mb-6">
          <button type="submit" class="button-light-blue w-full text-center">
            Log In
          </button>
        </div>

        <div class="flex justify-between items-center text-sm text-white">
          <?php if ($sign_up_link): ?>
            <a
              href="<?php echo esc_url($sign_up_link['url']); ?>"
              class="underline hover:no-underline"
              <?php if ($sign_up_link['target']): ?>target="<?php echo esc_attr($sign_up_link['target']); ?>"<?php endif; ?>
            >
              <?php echo esc_html($sign_up_link['title'] ? $sign_up_link['title'] : 'Sign Up'); ?>
            </a>
          <?php endif; ?>
          <button
            type="button"
            @click="showReset = true"
            class="underline hover:no-underline"
          >
            Reset Password
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Reset Password Form -->
  <div class="fire-container duration-300 ease-in-out transition-all" :class="{ 'opacity-0 pointer-events-none': !showReset }" x-transition x-cloak>
    <div class="col-[main] lg:col-[col-3/col-10] xl:col-[col-4/col-9]" x-transition x-cloak>
      <form method="post" action="<?php echo esc_url(wp_lostpassword_url()); ?>" class="reset-form space-y-6">
        <div class="text-center mb-6">
          <h2 class="heading-3 text-white">Reset Password</h2>
        </div>

        <div class="form-field">
          <label for="user_login_reset" class="block text-base text-white">Email</label>
          <input
            type="text"
            name="user_login"
            id="user_login_reset"
            placeholder="Email"
            class="w-full bg-transparent border-0 border-b-2 border-white py-2 px-0 text-lg text-white placeholder:text-gray-400 focus:outline-none focus:shadow-none focus:border-b-light-blue"
            value=""
            size="20"
            required
          />
        </div>

        <div class="form-field">
          <button type="submit" class="button-light-blue text-center w-full">
            Reset Password
          </button>
        </div>

        <div class="text-center">
          <button
            type="button"
            @click="showReset = false"
            class="text-sm text-white underline hover:no-underline"
          >
            Back to Login
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $section->end(); ?>

