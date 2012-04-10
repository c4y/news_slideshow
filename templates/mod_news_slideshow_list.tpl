<script type="text/javascript">
window.addEvent('domready', function(){
  var news_slideshow<?php echo $this->id; ?> = new news_slideshow('menu<?php echo $this->id; ?>','pictures<?php echo $this->id; ?>', 'loading<?php echo $this->id; ?>', 'infos<?php echo $this->id; ?>', { transition: '<?php echo $this->transition; ?>', auto:true, infobox:<?php echo ($this->showinfobox) ? 'true':'false'; ?>, autointerval: <?php echo $this->intervall; ?>, autostart:<?php echo ($this->autostart) ? 'true' : 'false'; ?>, tween:{duration:<?php echo $this->duration; ?>} });
});
</script>
<!-- indexer::stop -->
<div class="<?php echo $this->class; ?> block" id="slideshow"<?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<span id="loading<?php echo $this->id; ?>" class="loading">loading</span>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>
<?php echo $this->articles; ?> 

</div>
<!-- indexer::continue -->
