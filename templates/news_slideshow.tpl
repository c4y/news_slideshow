<ul id="pictures<?php echo $this->id1; ?>" class="news_slideshow_pictures">
<?php foreach ($this->items as $item): ?>
<li>
<?php echo $item['linkImage']; ?>
</li>
<?php endforeach; ?>
</ul>
<ul id="menu<?php echo $this->id1; ?>" class="news_slideshow_menu">
<?php foreach ($this->items as $item): ?>
<li><a href="<?php echo $item['link']; ?>"><?php if ($item['showmenupicture']) echo $item['menupicture']; ?><span><?php echo $item['newsHeadline']; ?></span></a></li>
<?php endforeach; ?>
</ul>
<ul <?php if (!$this->showinfobox) echo 'style="display:none" '; ?>id="infos<?php echo $this->id1; ?>" class="news_slideshow_infos">
<?php foreach ($this->items as $item): ?>
<li>
   <h3><?php if ($item['link']): ?><a href="<?php echo $item['link']; ?>"><?php endif; echo $item['newsHeadline']; ?> | <?php echo $item['archive']; ?><?php if ($item['link']): ?></a><?php endif; ?></h3>
   <p><?php echo $item['teaser']; ?></p>
</li>
<?php endforeach; ?>
</ul>