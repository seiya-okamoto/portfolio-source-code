<?php
/* Template Name: 会社概要 */
?>

<?php get_header(); ?>
<div class="breadcrumb-trail">
    <?php if(function_exists('bcn_display')) {
        bcn_display();
    }?>
</div>
<div id="cont_first" class="container">
    <div id="contents">
        <div id="cont_left">
            <h2>会社概要</h2>
            <table>
                <tr>
                    <th>会社名</th>
                    <td><?php the_field('add_company_name'); ?></td>
                </tr>
                <tr>
                    <th>本社</th>
                    <td>
                        <?php the_field('add_main_office_post_code'); ?><br>
                        <?php the_field('add_main_office_address'); ?>
                    </td>
                </tr>
                <tr>
                    <th>設立</th>
                    <td><?php the_field('add_established'); ?></td>
                </tr>
                <tr>
                    <th>資本金</th>
                    <td><?php the_field('add_capital'); ?></td>
                </tr>
                <tr>
                    <th>従業員数</th>
                    <td><?php the_field('add_member'); ?></td>
                </tr>
                <tr>
                    <th>電話番号</th>
                    <td><?php the_field('add_tel'); ?></td>
                </tr>
                <tr>
                    <th>代表者</th>
                    <td><?php the_field('add_representative'); ?></td>
                </tr>
            </table>
        </div>
        <?php get_sidebar(); ?>
    </div>
</div>
<?php get_footer(); ?>
<div id="pageTop">
    <a href="#">PAGE TOP</a>
</div>
</body>
</html>