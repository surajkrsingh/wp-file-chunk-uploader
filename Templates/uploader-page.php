<?php
/**
 * Uploader Page
 */
?>

<div class="wrap">
	<h1>Admin Page</h1>

	<div id="bb-chunk-uploader" style="border: 2px dashed #ccc; padding: 20px; text-align: center;">
		Drag and drop your ZIP file here, or click to upload.
		<input type="file" id="file-input" accept=".zip" >
	</div>

	<div id="upload-status"></div>

	<!-- Modal for progress -->
	<div id="upload-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; background: #fff; padding: 20px; border: 2px solid #ccc; text-align: center; z-index: 9999;">
		<h3>Uploading...</h3>
		<p id="upload-warning" style="color: red;">Do not close or reload this page until the upload is complete.</p>
		<progress id="progress-bar" value="0" max="100" style="width: 100%;"></progress>
		<p id="progress-size">Uploaded: 0MB / 0MB</p>
		<p id="progress-percentage">0% complete</p>
	</div>

	<h3>Uploaded Files:</h3>
	<ul id="uploaded-files-list"></ul>


</div>
