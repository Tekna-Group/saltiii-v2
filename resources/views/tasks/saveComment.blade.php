<script>
document.addEventListener('DOMContentLoaded', function () {
    const taskId = {{ $task->id }};
    const form = document.getElementById('taskCommentForm' + taskId);

    if (!form) {
        console.error('[ERROR] Form not found for task ID:', taskId);
        return;
    }

    console.log('[READY] Form initialized for task ID:', taskId);

    // Handle form submit
    form.addEventListener('submit', async function (event) {
        event.preventDefault(); // Prevent page refresh
        console.log('[SUBMIT] Intercepted submit for task ID:', taskId);

        const formData = new FormData(form);

        try {
            const response = await fetch(`{{ url('/task-comment/api/') }}/${taskId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();
            console.log('[RESPONSE] Data received:', data);

            if (data.success) {
                    const commentsContainer = document.getElementById('commentsContainer' + taskId);
                    const contentDiv = commentsContainer.querySelector('.simplebar-content:first-child');
                    const simpleBarInstance = commentsContainer.SimpleBar || SimpleBar.instances.get(commentsContainer);


                    if (commentsContainer) {
                        // Get SimpleBar instance
                        const simpleBar = SimpleBar.instances.get(commentsContainer);
                        const scrollElement = simpleBar.getScrollElement();

                        // Preserve scroll position
                        const previousScrollHeight = scrollElement.scrollHeight;

                        // Build new comment HTML
                        const newComment = document.createElement('div');

                        const commentText = data.comment.comment; 
                        newComment.classList.add('d-flex', 'mb-4');

                        let fileLink = '';
                        if (data.comment.file_path) {
                            fileLink = `
                                <a href="${data.comment.file_path}" 
                                target="_blank" 
                                class="btn btn-sm btn-success mt-2">
                                    View Attachment
                                </a>
                            `;
                        }
                        newComment.innerHTML = `
                        <div class="flex-shrink-0">
                                <img src="${data.comment.user_avatar ? `{{ asset('') }}${data.comment.user_avatar}` : `{{ asset('images/Favicon.png') }}`}" 
                                    onerror="this.src='{{ asset('images/Favicon.png') }}';" 
                                    alt="User Avatar" 
                                    class="avatar-xs rounded-circle material-shadow" />
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="fs-13">
                                    <a href="#">${data.comment.user_name}</a>
                                    <small class="text-muted">${data.comment.created_at}</small>
                                </h5>
                                <p class="text-muted">${commentText}</p>
                                ${fileLink}
                            </div>
                        `;

                        // Prepend the new comment
                        contentDiv.prepend(newComment);

                        setTimeout(() => {
                            if (simpleBarInstance) {
                                simpleBarInstance.getScrollElement().scrollTop = 0;
                            }
                        }, 10);
                        // Adjust scroll so user stays at the same position
                        const newScrollHeight = scrollElement.scrollHeight;
                        scrollElement.scrollTop += newScrollHeight - previousScrollHeight;
                    }

                // Reset the form
             

                // Scroll to top
                form.reset();
                // Assuming you already initialized FilePond
                const pond = FilePond.create(document.getElementById('proof' + taskId));

                // After successful comment post:
                pond.removeFiles(); // clears all selected files
                
                console.log('[RESET] Form cleared successfully.');

                // Show success toast
                Toastify({
                    text: "Comment posted successfully!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#4CAF50",
                    stopOnFocus: true
                }).showToast();

            } else {
                console.warn('[FAILURE] Server did not return success:', data);

                Toastify({
                    text: "Failed to post comment. Please try again.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#FF0000",
                    stopOnFocus: true
                }).showToast();
            }

        } catch (error) {
            console.error('[ERROR] AJAX request failed:', error);

            Toastify({
                text: "Something went wrong. Please try again later.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#FF0000",
                stopOnFocus: true
            }).showToast();
        }
    });
});
</script>