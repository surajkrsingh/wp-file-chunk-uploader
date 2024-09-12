/**
 * Main class.
 *
 * @package WP_FCU
 */

import ChunkUploader from './js/ChunkUploader';
jQuery(document).ready(function () {
    try {
        const uploader = new ChunkUploader({
            ajaxUrl: FCU_Objects?.ajaxURL || '',
            chunkSize: 1024 * 1024 * 10 // 10MB
        });

        if (uploader) {
            uploader.init();
        } else {
            console.error('ChunkUploader initialization failed.');
        }
    } catch (error) {
        console.error('Error initializing ChunkUploader:', error);
    }
});
