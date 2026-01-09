/**
 * Contact Form AJAX Handler
 *
 * Handles form submission via AJAX with:
 * - CSRF token management
 * - Validation error display
 * - Success feedback
 * - Form reset
 * - Loading states
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');

    if (!form) {
        return; // Exit if form doesn't exist on page
    }

    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.textContent;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Clear previous errors
        clearErrors();

        // Disable submit button and show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Sending...
        `;

        // Get form data
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        try {
            // Send AJAX request
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                // Success
                handleSuccess(result);
            } else {
                // Validation errors
                handleValidationErrors(result.errors);
            }

        } catch (error) {
            // Network or other errors
            handleError(error);
        } finally {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }
    });

    /**
     * Handle successful submission
     */
    function handleSuccess(result) {
        // Show success message
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.classList.remove('hidden');
            successMessage.querySelector('.message-text').textContent =
                result.message || 'Thank you for your submission! We will get back to you soon.';

            // Scroll to success message
            successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Reset form
        form.reset();

        // Hide success message after 5 seconds
        setTimeout(() => {
            if (successMessage) {
                successMessage.classList.add('hidden');
            }
        }, 5000);
    }

    /**
     * Handle validation errors
     */
    function handleValidationErrors(errors) {
        if (!errors) return;

        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                // Add error class to input
                input.classList.add('border-red-300', 'dark:border-red-600');

                // Create or update error message
                let errorElement = input.parentElement.querySelector('.error-message');
                if (!errorElement) {
                    errorElement = document.createElement('p');
                    errorElement.className = 'error-message mt-2 text-sm text-red-600 dark:text-red-400';
                    input.parentElement.appendChild(errorElement);
                }
                errorElement.textContent = errors[field][0];
            }
        });

        // Scroll to first error
        const firstError = form.querySelector('.border-red-300, .border-red-600');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    }

    /**
     * Handle network or other errors
     */
    function handleError(error) {
        console.error('Form submission error:', error);

        // Show generic error message
        const errorContainer = document.getElementById('error-message');
        if (errorContainer) {
            errorContainer.classList.remove('hidden');
            errorContainer.querySelector('.message-text').textContent =
                'An error occurred while submitting the form. Please try again.';

            errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Hide error after 5 seconds
            setTimeout(() => {
                errorContainer.classList.add('hidden');
            }, 5000);
        }
    }

    /**
     * Clear all validation errors
     */
    function clearErrors() {
        // Remove error classes from inputs
        form.querySelectorAll('.border-red-300, .border-red-600').forEach(input => {
            input.classList.remove('border-red-300', 'dark:border-red-600');
        });

        // Remove error messages
        form.querySelectorAll('.error-message').forEach(error => {
            error.remove();
        });

        // Hide error container
        const errorContainer = document.getElementById('error-message');
        if (errorContainer) {
            errorContainer.classList.add('hidden');
        }
    }

    /**
     * Clear errors when user starts typing
     */
    form.querySelectorAll('input, textarea').forEach(input => {
        input.addEventListener('input', function() {
            // Remove error class from this input
            this.classList.remove('border-red-300', 'dark:border-red-600');

            // Remove error message for this field
            const errorElement = this.parentElement.querySelector('.error-message');
            if (errorElement) {
                errorElement.remove();
            }
        });
    });
});
