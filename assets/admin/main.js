import './scss/dashboard.scss';
import './scss/settings.scss';
import './scss/common.scss';

import ChunkUploader from './js/ChunkUploader';

jQuery(document).ready(function () {
    const uploader = new ChunkUploader(
        {
            ajaxUrl: ajaxurl,
            chunkSize: 1024 * 1024 * 10 // 10MB
        }
    );

    uploader.init();
});
