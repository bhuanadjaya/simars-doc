<div id="page-form" class="page">
  <div style="max-width: 538px; margin: 0 auto">
    <div class="ina-card ina-card--variant-basic">
      <div class="ina-card__content">
        <form id="form-submission" class="space-y-6 w-full">
          <!-- Header -->
          <div class="mb-8">
            <h1 class="text-2xl font-bold text-content-primary mb-2">Buat akun Anda</h1>
            <p class="text-sm text-content-secondary">Masukkan informasi berikut untuk membuat akun.</p>
          </div>

          <!-- Nama Lengkap -->
          <div class="ina-text-field">
            <label for="name" class="ina-text-field__label">
              Nama Lengkap <span class="ina-text-field__required">*</span>
            </label>
            <div class="ina-text-field__wrapper ina-text-field__wrapper--size-md" id="name-wrapper">
              <input type="text" id="name" name="name" class="ina-text-field__input" placeholder="Dwi Anjasmara" value="Dwi Anjasmara" />
            </div>
            <div id="name-error" class="ina-text-field__status ina-text-field__status--error hidden"></div>
          </div>

          <!-- Email -->
          <div class="ina-text-field">
            <label for="email" class="ina-text-field__label">
              Email <span class="ina-text-field__required">*</span>
            </label>
            <div class="ina-text-field__wrapper ina-text-field__wrapper--size-md" id="email-wrapper">
              <input type="email" id="email" name="email" class="ina-text-field__input" placeholder="example@email.com" value="example@email.com" />
            </div>
            <div id="email-error" class="ina-text-field__status ina-text-field__status--error hidden"></div>
          </div>

          <!-- Kata Sandi -->
          <div class="ina-password-input">
            <label for="password" class="ina-password-input__label">
              Kata Sandi <span class="ina-text-field__required">*</span>
            </label>
            <div class="ina-password-input__wrapper ina-password-input__wrapper--size-md" id="password-wrapper">
              <input type="password" id="password" name="password" class="ina-password-input__input" placeholder="Isi kata sandi Anda" />
              <button type="button" class="ina-password-input__clear-button" style="display: none" aria-label="Clear input" data-target="password">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ina-password-input__clear-icon">
                  <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
              </button>
              <button type="button" class="ina-password-input__toggle-button" aria-label="Show password" data-target="password">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ina-password-input__visibility-icon">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
            <div class="ina-password-input__status-area">
              <div id="password-error" class="ina-password-input__status-message ina-password-input__status-message--error hidden"></div>
              <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter.</p>
            </div>
          </div>

          <!-- Konfirmasi Kata Sandi -->
          <div class="ina-password-input">
            <label for="confirmPassword" class="ina-password-input__label">
              Konfirmasi Kata Sandi <span class="ina-text-field__required">*</span>
            </label>
            <div class="ina-password-input__wrapper ina-password-input__wrapper--size-md" id="confirm-password-wrapper">
              <input type="password" id="confirmPassword" name="confirmPassword" class="ina-password-input__input" placeholder="Ulangi kata sandi Anda" />
              <button type="button" class="ina-password-input__clear-button" style="display: none" aria-label="Clear input" data-target="confirmPassword">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ina-password-input__clear-icon">
                  <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
              </button>
              <button type="button" class="ina-password-input__toggle-button" aria-label="Show password" data-target="confirmPassword">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ina-password-input__visibility-icon">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
            <div class="ina-password-input__status-area">
              <div id="confirmPassword-error" class="ina-password-input__status-message ina-password-input__status-message--error hidden"></div>
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" class="ina-button ina-button--primary ina-button--md w-full">
            Buat Akun
          </button>

          <!-- Divider -->
          <div class="flex items-center gap-4">
            <div class="flex-1 h-px bg-gray-300"></div>
            <span class="text-sm text-gray-500">Atau</span>
            <div class="flex-1 h-px bg-gray-300"></div>
          </div>

          <!-- Google Button -->
          <button type="button" class="ina-button ina-button--secondary ina-button--md w-full flex items-center justify-center gap-2">
            <svg width="20" height="20" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"></path>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"></path>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"></path>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"></path>
            </svg>
            Buat Akun dengan Google
          </button>

          <!-- Footer -->
          <p class="text-xs text-content-secondary text-center">
            Dengan mendaftar, Anda menyetujui
            <a href="#" class="text-blue-600 hover:underline">Ketentuan</a>
            dan
            <a href="#" class="text-blue-600 hover:underline">Kebijakan Privasi</a>
            kami.
          </p>
        </form>
      </div>
    </div>
  </div>
</div>
