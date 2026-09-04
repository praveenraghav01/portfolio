<?php
/**
 * Reusable template-tag helpers shared by index/archive/search/single.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* One row in the "git log" post list — used by index.php, archive.php, search.php */
function pdl_logrow() {
	?>
	<article <?php post_class( 'logrow' ); ?> data-reveal>
		<div class="logrow__meta">
			<span class="logrow__hash mono">#<?php echo esc_html( pdl_post_hash() ); ?></span>
			<time class="logrow__date mono" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></time>
		</div>
		<div class="logrow__main">
			<h2 class="logrow__title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>
			<p class="logrow__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?></p>
			<div class="logrow__foot">
				<?php
				$cats = get_the_category();
				if ( $cats ) {
					foreach ( array_slice( $cats, 0, 3 ) as $cat ) {
						echo '<a class="kchip" href="' . esc_url( get_category_link( $cat ) ) . '"><span class="d"></span>' . esc_html( $cat->name ) . '</a>';
					}
				}
				?>
				<span class="logrow__rt mono"><?php echo esc_html( pdl_reading_time() ); ?> min read</span>
				<?php if ( comments_open() || get_comments_number() ) : ?>
					<span class="logrow__rt mono"><?php comments_number( '0 comments', '1 comment', '% comments' ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
}

/* Mono "‹ prev · page 2/5 · next ›" pagination, styled like the site's chips */
function pdl_pagination() {
	global $wp_query;
	$big = 999999999;
	$links = paginate_links( array(
		'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format'    => '?paged=%#%',
		'current'   => max( 1, get_query_var( 'paged' ) ),
		'total'     => $wp_query->max_num_pages,
		'prev_text' => '&lsaquo; prev',
		'next_text' => 'next &rsaquo;',
		'type'      => 'array',
	) );
	if ( empty( $links ) ) return;
	echo '<nav class="pdl-pagination mono" aria-label="Posts pagination"><ul>';
	foreach ( $links as $link ) {
		echo '<li>' . $link . '</li>';
	}
	echo '</ul></nav>';
}

/* Prev/next post, styled like mini release cards */
function pdl_post_nav() {
	$prev = get_previous_post();
	$next = get_next_post();
	if ( ! $prev && ! $next ) return;
	?>
	<nav class="pdl-postnav" aria-label="Post navigation">
		<?php if ( $prev ) : ?>
			<a class="pdl-postnav__card is-prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>" data-cursor="open">
				<span class="pdl-postnav__dir mono">&lsaquo; older commit</span>
				<span class="pdl-postnav__title"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
			</a>
		<?php else : ?><span></span><?php endif; ?>
		<?php if ( $next ) : ?>
			<a class="pdl-postnav__card is-next" href="<?php echo esc_url( get_permalink( $next ) ); ?>" data-cursor="open">
				<span class="pdl-postnav__dir mono">newer commit &rsaquo;</span>
				<span class="pdl-postnav__title"><?php echo esc_html( get_the_title( $next ) ); ?></span>
			</a>
		<?php endif; ?>
	</nav>
	<?php
}

/* Related posts — same primary category, excluding current */
function pdl_related_posts( $limit = 3 ) {
	$cats = wp_get_post_categories( get_the_ID() );
	if ( empty( $cats ) ) return;
	$q = new WP_Query( array(
		'category__in'        => $cats,
		'post__not_in'        => array( get_the_ID() ),
		'posts_per_page'      => $limit,
		'ignore_sticky_posts'  => true,
		'no_found_rows'        => true,
	) );
	if ( ! $q->have_posts() ) return;
	?>
	<div class="pdl-related">
		<span class="credgroup__label">related commits</span>
		<div class="pdl-related__grid">
			<?php while ( $q->have_posts() ) : $q->the_post(); ?>
				<a class="pdl-related__card" href="<?php the_permalink(); ?>" data-cursor="open">
					<span class="mono pdl-related__hash">#<?php echo esc_html( pdl_post_hash() ); ?></span>
					<span class="pdl-related__title"><?php the_title(); ?></span>
				</a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
	<?php
}

/* Table of contents box for long posts */
function pdl_toc() {
	$toc = pdl_get_toc();
	if ( count( $toc ) < 3 ) return;
	?>
	<nav class="pdl-toc" aria-label="Table of contents">
		<span class="pdl-toc__label mono">$ man <?php echo esc_html( get_post_field( 'post_name' ) ); ?> &mdash; sections</span>
		<ul>
			<?php foreach ( $toc as $item ) : ?>
				<li class="is-h<?php echo (int) $item['level']; ?>"><a href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}

/* Custom threaded-comment markup, mono/terminal styled */
function pdl_comment_cb( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'pdl-comment' ); ?>>
		<div class="pdl-comment__row">
			<?php echo get_avatar( $comment, 40 ); ?>
			<div class="pdl-comment__body">
				<div class="pdl-comment__meta">
					<span class="pdl-comment__author"><?php comment_author(); ?></span>
					<span class="pdl-comment__date mono">
						<a href="<?php echo esc_url( get_comment_link( $comment ) ); ?>"><?php comment_time( 'Y-m-d \a\t H:i' ); ?></a>
					</span>
				</div>
				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="pdl-comment__pending mono"><em>Your comment is pending review.</em></p>
				<?php endif; ?>
				<div class="pdl-comment__text"><?php comment_text(); ?></div>
				<?php
				comment_reply_link( array_merge( $args, array(
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
					'before'    => '<div class="pdl-comment__reply">',
					'after'     => '</div>',
				) ) );
				?>
			</div>
		</div>
	<?php
	// closing tag handled by WP's walker (end_el is default for html5 comment list)
}
