// Custom JavaScript for the jewelry order management system

document.addEventListener("DOMContentLoaded", function () {
  // Image preview functionality
  const imagePreviewLinks = document.querySelectorAll(".image-preview-link");
  imagePreviewLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const imageUrl = this.getAttribute("data-image-url");
      const previewModal = new bootstrap.Modal(
        document.getElementById("imagePreviewModal"),
      );
      document.getElementById("previewImage").src = imageUrl;
      previewModal.show();
    });
  });

  // Delete confirmation
  const deleteButtons = document.querySelectorAll(".delete-btn");
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const orderId = this.getAttribute("data-id");
      const deleteForm = document.getElementById("deleteForm");
      deleteForm.querySelector('input[name="id"]').value = orderId;
      const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteConfirmModal"),
      );
      deleteModal.show();
    });
  });

  // Model selection in order form
  const modelCards = document.querySelectorAll(".model-card");
  modelCards.forEach((card) => {
    card.addEventListener("click", function () {
      // Remove selected class from all cards
      modelCards.forEach((c) => c.classList.remove("selected"));
      // Add selected class to clicked card
      this.classList.add("selected");
      // Update hidden input value
      const modelId = this.getAttribute("data-model-id");
      document.getElementById("modelInput").value = modelId;
    });
  });

  // Image upload preview
  const imageUpload = document.getElementById("image-upload");
  if (imageUpload) {
    imageUpload.addEventListener("change", function () {
      const previewContainer = document.getElementById(
        "image-preview-container",
      );
      previewContainer.innerHTML = "";

      if (this.files) {
        Array.from(this.files).forEach((file, index) => {
          if (file.type.match("image.*")) {
            const reader = new FileReader();
            reader.onload = function (e) {
              const div = document.createElement("div");
              div.className = "col-6 col-md-3 mb-3 position-relative";
              div.innerHTML = `
                                <div class="card h-100">
                                    <img src="${e.target.result}" class="card-img-top" alt="Preview">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-image" data-index="${index}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `;
              previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
          }
        });
      }
    });

    // Remove image from preview
    document.addEventListener("click", function (e) {
      if (e.target.closest(".remove-image")) {
        const button = e.target.closest(".remove-image");
        const index = button.getAttribute("data-index");
        // Note: In a real implementation, you would need to handle removing the file from the FileList
        button.closest(".col-6").remove();
      }
    });
  }
});
