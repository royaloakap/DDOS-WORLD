<?php if($TASKINC){ ?>
    <html>
    <head>
       <meta charset="utf-8"/>
       <title><?=$ST->get("title")?> - Admin panel</title>
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/bootstrap.min.css">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/bootstrap-theme.min.css">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/style.css">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/flags.css">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
                          <script>ajaxFile='<?=$ST->get("url")?>/admincp/ajax.php';</script>
                    <script src="https://code.jquery.com/jquery.js"></script>
                    <script src="<?=$ST->get("url")?>/dist/js/bootstrap.min.js"></script>
                    <script>
                    $(window).load(function() {
                        $("html, body").animate({ scrollTop: $(document).height() }, 1000);
                        
                        $('.btnclose').click(function(){
                            close();
                        });
                      });
                      
                      
                      function toEnd(){
                          $('html, body').animate({scrollTop:$(document).height()}, 'slow');
                      }
                      function close(){
                            window.close();
                      }
                    </script>

    </head>
    <body>
            <nav class="navbar navbar-default  navbar-fixed-top" role="navigation" style="margin-left:5px;margin-right: 5px">
                <p class="navbar-text text-center"><?=$scriptMsg?></p>
            </nav>
        <div class="container" style="margin-top: 0px;margin-top: 70px">
<?php
                  $countMsg = ($TASKINFO->count=="all") ? $ln['all'] : $TASKINFO->count;
                                            if($TASKINFO->gander=="male")
                                                $ganderMsg = $ln['male'];
                                                elseif ($TASKINFO->gander=="female")
                                                $ganderMsg = $ln['female'];
                                            else {
                                                $ganderMsg = $ln['two'];
                                            }

                                            $taskforMsg = ($TASKINFO->taskfor=="users") ? $ln['users'] : $ln['pages'];
                                                
                                echo '<div class="panel panel-default item  user-item">
                                        <ul class="list-group" >
                                             <li class="list-group-item text-right">#'.$TASKINFO->id.'</li>
                                             <li class="list-group-item text-right">'.$ln['postsss_idss'].' : '.$TASKINFO->posts.'</li>
                                             <li class="list-group-item text-right" style="direction: rtl">'.$ln['count_chose'].' : '.$countMsg.'</li>
                                             <li class="list-group-item text-right">'.$ln['gander'].' : '.$ganderMsg.'</li>
                                             <li class="list-group-item text-right">'.$ln['taskfor'].' : '.$taskforMsg.'</li>
                                             <li class="list-group-item text-right">'.cptime($TASKINFO->time).'</li>
                                         
</ul>
                                       </div>';
?>
            <div class="box " style="direction: rtl;padding: 10px">
    
<?php } ?>