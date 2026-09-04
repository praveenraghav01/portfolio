<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<form role="search" method="get" class="pdl-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<span class="pdl-searchform__prompt mono">grep&gt;</span>
	<label class="sr-only" for="pdl-search-input">Search</label>
	<input type="search" id="pdl-search-input" class="pdl-searchform__input" placeholder="search posts&hellip;" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" autocomplete="off">
	<button type="submit" class="pdl-searchform__submit" aria-label="Search" data-cursor="search">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
	</button>
</form>
