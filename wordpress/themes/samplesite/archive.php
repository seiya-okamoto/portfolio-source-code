<?php get_header(); ?>
<div class="breadcrumb-trail">
    <?php if(function_exists('bcn_display')) {
        bcn_display();
    }?>
</div>
<div id="cont_first" class="container">
    <div id="contents">
        <div id="cont_left">
            <div class="information">
                <h2>INFORMATION</h2>
                <dl>
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <dt><?php the_time('Y-m-d'); ?></dt> 
                    <dd>
                        <span class="tab tag_<?php $cat = get_the_category(); $cat = $cat[0]; { echo $cat->slug; } ?>"> 
                        <?php $cat = get_the_category(); $cat = $cat[0]; { echo $cat->cat_name; } ?> </span>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>を掲載しました。
                    </dd>
                    <?php endwhile; endif; ?>
                </dl>
                <?php wp_pagenavi(); ?>
            </div>
        </div>
        <?php get_sidebar(); ?>
    </div>
</div>
<?php get_footer(); ?>
<div id="pageTop">
    <a href="#">PAGE TOP</a>
</div>