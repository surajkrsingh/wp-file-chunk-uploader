/**
 * ChunkUploader class.
 * 
 * @package WP_FCU
 */
class ChunkUploader {

    /**
     * Initializes the ChunkUploader class with the provided options.
     *
     * @param {Object} options - Configuration options for the ChunkUploader.
     * @param {number} options.chunkSize - The size of each chunk in bytes (default: 10MB).
     * @param {string} options.ajaxUrl - The WordPress AJAX URL for handling uploads.
     *
     * @return {void}
     */
    constructor(options) {
        this.chunkSize = options.chunkSize || 1024 * 1024 * 10; // Default to 10MB per chunk
        this.uploader = jQuery('#bb-chunk-uploader');
        this.fileInput = jQuery('#file-input');
        this.uploadStatus = jQuery('#upload-status');
        this.uploadedFilesList = jQuery('#uploaded-files-list');
        this.progressBar = jQuery('#progress-bar');
        this.progressPercentage = jQuery('#progress-percentage');
        this.progressSize = jQuery('#progress-size');
        this.preventPageReload = false;
        this.uploadModal = jQuery('#upload-modal');
        this.totalFileSize = 0;
        this.ajaxUrl = options.ajaxUrl; // Pass WordPress ajax URL
    }

    /**
     * Initializes the ChunkUploader by binding the necessary events.
     *
     * @return {void}
     */
    init() {
        this.bindEvents();
    }

    /**
     * Binds the necessary events to the ChunkUploader instance.
     * 
     * This function sets up event listeners for drag-and-drop interactions, 
     * file input changes, and click events. It also prevents page reload 
     * when an upload is in progress.
     * 
     * @return {void}
     */
    bindEvents() {
        // Handle drag-and-drop events
        this.uploader.on('dragover', this.handleDragOver.bind(this));
        this.uploader.on('dragleave', this.handleDragLeave.bind(this));
        this.uploader.on('drop', this.handleFileDrop.bind(this));
        this.uploader.on('click', this.handleClick.bind(this));
        this.fileInput.on('change', this.handleFileInput.bind(this));

        // Prevent page reload when upload is in progress
        window.onbeforeunload = () => {
            if (this.preventPageReload) {
                return 'Your file upload is in progress. Do you want to leave the page?';
            }
        };
    }

    handleDragOver(event) {
        event.preventDefault();
        event.stopPropagation();
        this.uploader.css('border-color', '#00f');
    }

    handleDragLeave(event) {
        event.preventDefault();
        event.stopPropagation();
        this.uploader.css('border-color', '#ccc');
    }

    handleFileDrop(event) {
        event.preventDefault();
        event.stopPropagation();
        this.uploader.css('border-color', '#ccc');
        const files = event.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            this.handleFile(files[0]);
        }
    }

    handleClick() {
        if (this.fileInput) {
            this.fileInput.trigger('click');
        } else {
            console.error("fileInput is not defined.");
        }
    }

    handleFileInput(event) {
        const file = event.target.files[0];
        if (file) {
            this.handleFile(file);
        }
    }

    handleFile(file) {
        if (file.type !== 'application/zip') {
            alert('Please upload a valid .zip file.');
            return;
        }

        this.totalFileSize = file.size;
        this.totalChunks = Math.ceil(this.totalFileSize / this.chunkSize);
        this.currentChunk = 0;
        this.progressBar.val(0);
        this.updateProgress(0, 0, this.totalFileSize);
        this.uploadModal.show();
        this.preventPageReload = true;

        this.uploadNextChunk(file);
    }

    uploadNextChunk(file) {
        const start = this.currentChunk * this.chunkSize;
        const end = Math.min(start + this.chunkSize, this.totalFileSize);
        const chunk = file.slice(start, end);

        const formData = new FormData();
        formData.append('file', chunk);
        formData.append('fileName', file.name);
        formData.append('chunkIndex', this.currentChunk);
        formData.append('totalChunks', this.totalChunks);
        formData.append('action', 'chunk_upload');

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => this.handleUploadSuccess(response, file),
            error: this.handleUploadError.bind(this)
        });
    }

    handleUploadSuccess(response, file) {
        if (response.success) {
            this.currentChunk++;
            const uploadedSize = this.currentChunk * this.chunkSize;
            const progress = (this.currentChunk / this.totalChunks) * 100;
            
            this.updateProgress(progress, uploadedSize, this.totalFileSize);

            if (this.currentChunk < this.totalChunks) {
                this.uploadNextChunk(file);
            } else {
                this.completeUpload(file);
            }
        } else {
            this.uploadStatus.append(`<p>Error: ${response.data}</p>`);
            this.preventPageReload = false;
            this.uploadModal.hide();
        }
    }

    handleUploadError() {
        this.uploadStatus.append('<p>There was an error uploading the file.</p>');
        this.preventPageReload = false;
        this.uploadModal.hide();
    }

    updateProgress(progress, uploadedSize, totalSize) {
        this.progressBar.val(progress);
        this.progressPercentage.text(`${Math.round(progress)}% complete`);
        this.progressSize.text(`Uploaded: ${(uploadedSize / (1024 * 1024)).toFixed(2)}MB / ${(totalSize / (1024 * 1024)).toFixed(2)}MB`);
    }

    completeUpload(file) {
        this.uploadStatus.append(`<p>Upload complete for ${file.name}!</p>`);
        this.updateProgress(100, this.totalFileSize, this.totalFileSize);
        this.addUploadedFileToList(file.name);
        this.preventPageReload = false;
        this.uploadModal.hide();
    }

    addUploadedFileToList(fileName) {
        const listItem = jQuery('<li></li>').text(fileName);
        this.uploadedFilesList.append(listItem);
    }
}
