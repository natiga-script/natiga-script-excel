<?php if(!defined('TEMP_PATH')){die('Forbidden');}
$temp_url = !empty($_temp_vars['temp_url'])?$_temp_vars['temp_url']:'templates/assets/';
if(isset($_temp_vars) && !empty($_temp_vars['student_result'])){
    $std_data = $_temp_vars['student_result'];
    $std_data['data'] = array_map(array('Natiga_Security','escapeHTML'),$std_data['data']);
    $std_data['headers'] = array_map(array('Natiga_Security','escapeHTML'),$std_data['headers']);
?>
    <div class="container">
        <header class="major">
            <h2><?= $std_data['data']['name'];?></h2>
            <p><?= $std_data['data']['grade'];?>
            <br />
            <span style="color: #ccc;">(<?= $std_data['headers'][0].' : '.$std_data['data']['num'];?>)</span>
            </p>
        </header>
        <div class="row">
            <div class="2u">&nbsp;</div>
            <div class="8u 12u$(medium)">
                <div class="table-wrapper">
                    <table class="alt">
                        <?php
                        //remove user name, seat No, grade from headers and data, and leave only subjects
                        unset($std_data['headers'][0],$std_data['headers'][1],$std_data['headers'][2],$std_data['data']['num'],$std_data['data']['name'],$std_data['grade']);
                        if(!empty($std_data['headers']) && !empty($std_data['data'])){
                            $i =0;
                            foreach($std_data['headers'] as $subject_name){
                                $i++;
                                if(!is_null($_temp_vars['student_result']['data']['sub'.$i])){
                                    $show_sub_as_separator = (floatval($_temp_vars['student_result']['data']['sub'.$i])===$_CONFIG['magic_number']);
                                ?>
                                    <tr>
                                        <td <?= ($show_sub_as_separator)? 'colspan="2"':'';?>><?= $subject_name;?></td>
                                        <?php if(!$show_sub_as_separator){ ?>
                                        <td><?= $std_data['data']['sub'.$i];?></td>
                                        <?php } ?>
                                    </tr>
                                <?php
                                }
                            }
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php }?>
<script type="text/javascript">
    document.querySelector('#natiga a.goto-next').click();
</script>