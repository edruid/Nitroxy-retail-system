<p>
	<?php if($page > 0): ?>
		<a href="<?=sprintf($url_format, 0)?>">&lt;&lt;&lt;</a>
		<a href="<?=sprintf($url_format, max(0, $page-1))?>">Föregående</a>
	<?php else: ?>
		&lt;&lt;&lt;
		Föregående
	<?php endif ?>
	<?php if($page < $last_page): ?>
		<a href="<?=sprintf($url_format, min($last_page, $page+1))?>">Nästa</a>
		<a href="<?=sprintf($url_format, $last_page)?>">&gt;&gt;&gt;</a>
	<?php else: ?>
		Nästa
		&gt;&gt;&gt;
	<?php endif ?>
</p>
