<select class="input-field language-selector">
	<option value="ar" <?php if ($this->Session->read('Config.language') == "ar") {echo "selected"; } ?>>العربية‏ Arabic</option>
	<option value="en" <?php if ($this->Session->read('Config.language') == "en") {echo "selected"; } ?>>English</option>
	<option value="ru" <?php if ($this->Session->read('Config.language') == "ru") {echo "selected"; } ?>>Русский</option>
</select>

<script type="text/javascript">

	$(document).ready(function() {

		$('.language-selector').change(function() {
			document.location = '<?php echo $this->request->here; ?>?l=' + $(this).val();
		});

	});

</script>
