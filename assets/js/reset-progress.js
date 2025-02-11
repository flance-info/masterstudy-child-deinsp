document.addEventListener('DOMContentLoaded', function() {
    const resetButton = document.getElementById('reset-progress');
    const notification = document.getElementById('reset-notification');

    if (resetButton) {
        resetButton.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            const userId = this.getAttribute('data-user-id');

            const payload = {
                action: 'stm_lms_dashboard_reset_student_progress_child',
                nonce: stm_lms_nonces['stm_lms_dashboard_reset_student_progress'],
                course_id: courseId,
                user_id: userId
            };

            // Change button text to indicate loading
            const originalButtonText = resetButton.textContent;
            resetButton.textContent = 'Loading...';
            resetButton.disabled = true; // Disable the button to prevent multiple clicks
            notification.style.display = 'none';

            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(payload).toString()
            })
            .then(response => response.json())
            .then(data => {
                // Restore button text
                resetButton.textContent = originalButtonText;
                resetButton.disabled = false;

                if (data.success) {
                    // Show success notification
                    notification.style.display = 'block';
                    notification.textContent = 'Progress has been reset successfully.';
                    notification.classList.remove('error');
                    notification.classList.add('success');

                    // Optionally, refresh the page after a short delay
                    setTimeout(() => location.reload(), 500);
                } else {
                    // Show error notification
                    notification.style.display = 'block';
                    notification.textContent = 'Failed to reset progress: ' + (data.data || 'Unknown error');
                    notification.classList.add('error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Restore button text and show error notification
                resetButton.textContent = originalButtonText;
                resetButton.disabled = false;
                notification.style.display = 'block';
                notification.textContent = 'An error occurred. Please try again.';
                notification.classList.add('error');
            });
        });
    }
});
