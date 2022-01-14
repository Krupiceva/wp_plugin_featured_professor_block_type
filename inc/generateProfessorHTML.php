<?php

function generateProfessorHTML($id){
    $profPost = new WP_Query(array(
        'post_type' => 'teacher',
        'p' => $id
    ));

    while($profPost->have_posts()){
        $profPost->the_post();
        ob_start() ?>
        <div class="professor-callout">
            <div class="professor-callout__photo" style="background-image: url(<?php the_post_thumbnail_url('teacherPortrait'); ?>)"></div>
            <div class="professor-callout__text">
                <h5><?php the_title(); ?></h5>
                <p><?php echo wp_trim_words(get_the_content(), 30); ?></p>

                <?php 
                $relatedCourses = get_field('related_courses'); 
                if($relatedCourses){ ?>
                    <p><?php echo esc_html(get_the_title()); ?> teaches: 
                        <?php foreach($relatedCourses as $key => $course){
                            echo get_the_title($course);
                            if($key != array_key_last($relatedCourses) && count($relatedCourses) > 1){
                                echo ', ';
                            }
                        } ?>.
                    </p>
                <?php }
                ?>

                <p><strong><a href="<?php the_permalink(); ?>">Learn more about <?php the_title(); ?> &raquo;</a></strong></p>

            </div>
        </div>
        <?php 
        wp_reset_postdata();
        return ob_get_clean();
    }
}