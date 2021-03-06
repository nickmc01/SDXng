<?php
require_once '../Init.php';
$user = new User();
if($user->isLoggedIn() && $user->hasPermission('edit')) {
    if(Input::exists()) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'techlist' => array(
                'required' => true
            ),
            'startmonthlist' => array(
                'required' => true
            ),
            'startdaylist' => array(
                'required' => true
            ),
            'startyearlist' => array(
                'required' => true
            ),
            'notes' => array(
                'required' => true
            )
        ));
        if($validation->passed()) {
            $startday = strtotime($_POST['startyearlist'].'-'.$_POST['startmonthlist'].'-'.$_POST['startdaylist']);

            if($_POST['stopmonthlist'] != '- Month -') {
                $stopday = strtotime($_POST['stopyearlist'].'-'.$_POST['stopmonthlist'].'-'.$_POST['stopdaylist']);
            } else {
                $stopday = $startday;
            }

            if(isset($_POST["isConfirmed"]))
            {
                $isConfirmed = 1;
            } else {
                $sitesCheck = DB::getInstance()->getAssoc("SELECT callAhead FROM sites WHERE site_name = ?", array($_POST['sitelist']));
                foreach($sitesCheck->results() as $results) {
                    $sites_check[] = $results;
                }
                if(!empty($sites_check)) {
                    foreach ($sites_check as $check) {
                        if ($check['callAhead'] == 1) {
                            $isConfirmed = 1;
                        } else {
                            $isConfirmed = 0;
                        }
                    }
                } else {
                    $isConfirmed = 0;
                }
            }

            if ($_POST['techlist'] == 'Field Specialist') {
                $userRows = DB::getInstance()->getAssoc("SELECT full_name, work_group FROM users WHERE work_group LIKE 'Field Specialist%'");
                foreach($userRows->results() as $results) {
                    $user_rows[] = $results;
                }
                foreach ($user_rows as $row) {
                    for($i = $startday; $i <= $stopday; $i = strtotime("+1 day",$i)) {
                        $schedule = DB::getInstance()->insertAssoc('sched', array(
                            'date' => date('Y-m-d',$i),
                            'site' => $_POST['sitelist'],
                            'full_name' => $row['full_name'],
                            'type' => $_POST['type'],
                            'nid' => $_POST['nid'],
                            'notes' => $_POST['notes'],
                            'start_time' => $_POST['start_time'],
                            'stop_time' => $_POST['stop_time'],
                            'isConfirmed' => $isConfirmed,
                            'madeUser' => $user->data()->full_name,
			    'oscname' => $_POST['oscname'],
			    'oscnumber' => $_POST['oscnumber']
                        ));
                    }
                }
            }

            if ($_POST['techlist'] == 'Office') {
                $userRows = DB::getInstance()->getAssoc("SELECT full_name, work_group FROM users WHERE work_group LIKE 'Office%'");
                foreach($userRows->results() as $results) {
                    $user_rows[] = $results;
                }
                foreach ($user_rows as $row) {
                    for($i = $startday; $i <= $stopday; $i = strtotime("+1 day",$i)) {
                        $schedule = DB::getInstance()->insertAssoc('sched', array(
                            'date' => date('Y-m-d',$i),
                            'site' => $_POST['sitelist'],
                            'full_name' => $row['full_name'],
                            'type' => $_POST['type'],
                            'nid' => $_POST['nid'],
                            'notes' => $_POST['notes'],
                            'start_time' => $_POST['start_time'],
                            'stop_time' => $_POST['stop_time'],
                            'isConfirmed' => $isConfirmed,
                            'madeUser' => $user->data()->full_name,
			    'oscname' => $_POST['oscname'],
			    'oscnumber' => $_POST['oscnumber']
                        ));
                    }
                }
            }

            if ($_POST['techlist'] == 'All') {
                $userRows = DB::getInstance()->getAssoc("SELECT full_name FROM users");
                foreach($userRows->results() as $results) {
                    $user_rows[] = $results;
                }
                foreach ($user_rows as $row) {
                    for($i = $startday; $i <= $stopday; $i = strtotime("+1 day",$i)) {
                        $schedule = DB::getInstance()->insertAssoc('sched', array(
                            'date' => date('Y-m-d',$i),
                            'site' => $_POST['sitelist'],
                            'full_name' => $row['full_name'],
                            'type' => $_POST['type'],
                            'nid' => $_POST['nid'],
                            'notes' => $_POST['notes'],
                            'start_time' => $_POST['start_time'],
                            'stop_time' => $_POST['stop_time'],
                            'isConfirmed' => $isConfirmed,
                            'madeUser' => $user->data()->full_name,
			    'oscname' => $_POST['oscname'],
			    'oscnumber' => $_POST['oscnumber']
                        ));
                    }
                }
            } else {
                for($i = $startday; $i <= $stopday; $i = strtotime("+1 day",$i)) {
                    $schedule = DB::getInstance()->insertAssoc('sched', array(
                        'date' => date('Y-m-d',$i),
                        'site' => $_POST['sitelist'],
                        'full_name' => $_POST['techlist'],
                        'type' => $_POST['type'],
                        'nid' => $_POST['nid'],
                        'notes' => $_POST['notes'],
                        'start_time' => $_POST['start_time'],
                        'stop_time' => $_POST['stop_time'],
                        'isConfirmed' => $isConfirmed,
                        'madeUser' => $user->data()->full_name,
			'oscname' => $_POST['oscname'],
			'oscnumber' => $_POST['oscnumber']
                    ));
                }
            }
            Redirect::to('../schedule/');
        } else {
            foreach($validation->errors() as $error) {
                $error = ucfirst($error);
                echo '<script type="text/javascript"> alert(\''.$error.'\'); </script>';
            }
        }
    }

    if (Input::get('type') == 'btn-sch btn-brown'){
        $type = "Non-Emergency";
    }
    if (Input::get('type') == 'btn-sch btn-red') {
        $type = "Emergency";
    }
    if (Input::get('type') == 'btn-sch btn-green') {
        $type = "PM";
    }
    if (Input::get('type') == 'btn-sch btn-orange') {
        $type = "Holiday";
    }
    if (Input::get('type') == 'btn-sch btn-purple') {
        $type = "PTO";
    }
    if (Input::get('type') == 'btn-sch btn-primary') {
        $type = "T&M";
    }
    if (Input::get('type') == 'btn-sch btn-blue') {
        $type = "Installed";
    }

    if (Input::get('type') == 'btn-sch btn-black'){
        $type = "Training";
    }

    if (Input::get('type') == 'btn-sch btn-other'){
        $type = "Other";
    }

    $sitelist = Input::get('sitelist');
    $techlist = Input::get('techlist');
    $startmonthlist = Input::get('startmonthlist');
    $startdaylist = Input::get('startdaylist');
    $startyearlist = Input::get('startyearlist');
    $start_time = Input::get('start_time');
    $stop_time = Input::get('stop_time');


    $startdays = date('j', strtotime(Input::get('startdaylist')));
    $startmonths = date('F', strtotime(Input::get('startmonthlist')));
    $startyears = date('y', strtotime(Input::get('startyearlist')));

    if(is_numeric($stopday)) {
        $stopdays = date('j', strtotime(Input::get('stopdaylist')));
        $stopmonths = date('F', strtotime(Input::get('stopmonthlist')));
        $stopyears = date('y', strtotime(Input::get('stopyearlist')));

        $stopmonthlist = Input::get('stopmonthlist');
        $stopdaylist = Input::get('stopdaylist');
        $stopyearlist = Input::get('stopyearlist');
    } else {
        $stopdays = "- Day -";
        $stopmonths = "- Month -";
        $stopyears = "- Year -";

        $stopmonthlist =  "- Month -";
        $stopdaylist = "- Day -";
        $stopyearlist = "- Year -";
    }

    if ($user->hasPermission('edit')) {
        $userRows = DB::getInstance()->getAssoc("SELECT * FROM users ORDER BY full_name ASC");
        foreach($userRows->results() as $results) {
            $user_rows[] = $results;
        }
        $sitesRows = DB::getInstance()->getAssoc("SELECT * FROM sites ORDER BY site_name ASC");
        foreach($sitesRows->results() as $results) {
            $sites_rows[] = $results;
        }

        $page = new Page;
        $page->setTitle('New Schedule');
        $page->startBody();
        echo $page->render('../includes/menu.php');
        ?>
        <div id="content">
            <div id="contentHeader"></div>
            <div class="container">
                <div class="grid-6"></div>
                <div class="grid-12">
                    <div class="row">
                        <div class="widget">
                            <form action="" method="post" accept-charset="utf-8" class="form uniformForm">
                                <div class="widget-content">
                                    <div class="field-group">
                                        <div class="field">
                                            <label for="site">Choose Site</label>
                                            <select name="sitelist" id="sitelist">
                                                <?php
                                                if(!empty($sitelist)) {
                                                    echo '<option value="'.escape(Input::get('sitelist')).'">';
                                                    echo escape(Input::get('sitelist'));
                                                    echo '</option>';
                                                }
                                                foreach($sites_rows as $site_row) {
                                                    $site = escape($site_row['site_name']);
                                                    echo '<option value="'.$site.'">';
                                                    echo escape($site_row['site_name']);
                                                    echo '</option>';
                                                }
                                                echo '<input type="hidden" name="site" value="'.$site_name.'">';
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="field-group">
                                        <div class="field">
                                            <label for="full_name">Choose Technician</label>
                                            <select name="techlist" id="techlist">
                                                <?php
                                                if(!empty($techlist)) {
                                                    echo '<option value="'.escape(Input::get('techlist')).'">';
                                                    echo escape(Input::get('techlist'));
                                                    echo '</option>';
                                                }
                                                ?>
                                                <option value="All">All Employees</option>
                                                <option value="Field Specialist">All Techs</option>
                                                <option value="Office">All SAE's</option>
                                                <?php
                                                foreach($user_rows as $user_row) {
                                                    $user_name = escape($user_row['full_name']);
                                                    echo '<option value="'.$user_name.'">';
                                                    echo $user_name;
                                                    echo '</option>';
                                                }
                                                echo '<input type="hidden" name="full_name" value="'.$user_name.'">';
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="field-group">
                                        <div class="field">
                                            <label for="city">Call Type</label>
                                            <select name="type" id="type" required>
                                                <?php
                                                if(!empty($type)) {
                                                    echo '<option value="'.escape(Input::get('type')).'">';
                                                    echo decode($type);
                                                    echo '</option>';
                                                }
                                                ?>
                                                <option value="btn-sch btn-brown">Non-Emergency</option>
                                                <option value="btn-sch btn-red">Emergency</option>
                                                <option value="btn-sch btn-green">PM</option>
                                                <option value="btn-sch btn-blue">Installed</option>
                                                <option value="btn-sch btn-orange">Holiday</option>
                                                <option value="btn-sch btn-purple">PTO</option>
                                                <option value="btn-sch btn-primary"><?php echo decode('T&M'); ?></option>
                                                <option value="btn-sch btn-black">Training</option>
                                                <option value="btn-sch btn-other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="field-group">
                                        <div class="field">
                                            <label for="date">Start Date</label>
                                            <select name="startmonthlist" id="startmonthlist">
                                                <?php
                                                if(!empty($startmonthlist)) {
                                                    echo '<option value="'.escape(Input::get('startmonthlist')).'">';
                                                    echo $startmonth;
                                                    echo '</option>';
                                                }
                                                ?>
                                                <option value="<?php echo date('m',strtotime('today')); ?>"><?php echo date('M',strtotime('today')); ?></option>
                                                <option value="01">January</option>
                                                <option value="02">February</option>
                                                <option value="03">March</option>
                                                <option value="04">April</option>
                                                <option value="05">May</option>
                                                <option value="06">June</option>
                                                <option value="07">July</option>
                                                <option value="08">August</option>
                                                <option value="09">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                            <select name="startdaylist" id="startdaylist">
                                                <?php
                                                if(!empty($startdaylist)) {
                                                    echo '<option value="'.escape(Input::get('startdaylist')).'">';
                                                    echo $startday;
                                                    echo '</option>';
                                                }
                                                ?>
                                                <option value="<?php echo date('d',strtotime('today')); ?>"><?php echo date('d',strtotime('today')); ?></option>
                                                <?php
                                                for ($i=01; $i<32; $i++) {
                                                    $value = $i;
                                                    if ($i < 10) {
                                                        $value = str_pad($i, 2, "0", STR_PAD_LEFT);
                                                    }
                                                    echo '<option value="'.$value.'">'.$value.'</option>';
                                                } ?>
                                            </select>
                                            <select name="startyearlist" id="startyearlist">
                                                <?php
                                                if(!empty($startyearlist)) {
                                                    echo '<option value="'.escape(Input::get('startyearlist')).'">';
                                                    echo $startyear;
                                                    echo '</option>';
                                                }
                                                ?>
                                                <option value="<?php echo date('Y',strtotime('today')); ?>"><?php echo date('Y',strtotime('today')); ?></option>
                                                <?php
                                                for ($i=2013; $i<2021; $i++) {
                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="field-group">
                                        <div class="field">
                                            <label for="date">End Date</label>
                                            <select name="stopmonthlist" id="stopmonthlist">
                                                <option>- Month -</option>
                                                <option value="01">January</option>
                                                <option value="02">February</option>
                                                <option value="03">March</option>
                                                <option value="04">April</option>
                                                <option value="05">May</option>
                                                <option value="06">June</option>
                                                <option value="07">July</option>
                                                <option value="08">August</option>
                                                <option value="09">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                            <select name="stopdaylist" id="stopdaylist">
                                                <option>- Day -</option>
                                                <?php
                                                for ($i=01; $i<32; $i++) {
                                                    $value = $i;
                                                    if ($i < 10) {
                                                        $value = str_pad($i, 2, "0", STR_PAD_LEFT);
                                                    }
                                                    echo '<option value="'.$value.'">'.$value.'</option>';
                                                } ?>
                                            </select>
                                            <select name="stopyearlist" id="stopyearlist">
                                                <option>- Year -</option>
                                                <?php
                                                for ($i=2013; $i<2021; $i++) {
                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="field-group">
                                        <div class="field">
                                            <label for="start_time">Start Time</label>
                                            <input type="text" name="start_time" size="63" value="<?php if(!empty($start_time)) {echo escape(Input::get('start_time'));} else {echo "0700";} ?>" id="start_time" tabindex="1" placeholder="Start Time" />
                                        </div>
                                    </div>
                                    <div class="field-group">
                                        <div class="field">
                                            <label for="stop_time">Stop Time</label>
                                            <input type="text" name="stop_time" size="63" value="<?php if(!empty($stop_time)) {echo escape(Input::get('stop_time'));} else {echo "1500";} ?>" id="stop_time" tabindex="1" placeholder="Stop Time" />
                                        </div>
                                    </div>

                                    <div class="field-group">
                                        <div class="field">
                                            <label for="nid">Svc Ord/Network Number</label>
                                            <input type="text" name="nid" size="63" value="<?php echo Input::get('nid'); ?>" id="nid" tabindex="1" placeholder="Network ID" />
                                        </div>
                                    </div>

 				    <div class="field-group">
                                        <div class="field">
                                            <label for="nid">On-Site Contact Name</label>
                                            <input type="text" name="oscname" size="63" value="<?php echo Input::get('oscname'); ?>" id="nid" tabindex="1" placeholder="On-Site Contact Name" />
                                        </div>
                                    </div>

 				    <div class="field-group">
                                        <div class="field">
                                            <label for="nid">On-Site Contact Number</label>
                                            <input type="text" name="oscnumber" size="63" value="<?php echo Input::get('oscnumber'); ?>" id="nid" tabindex="1" placeholder="On-Site Contact Number" />
                                        </div>
                                    </div>

                                    <div class="field-group">
                                        <div class="field">
                                            <label for="notes">Description
                                            <textarea name="notes" value="" id="notes" placeholder="Notes"  rows="10" cols="63" tabindex="1" /><?php echo Input::get('notes'); ?></textarea>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="field-group">
                                        <div class="field">
                                            <label for="isConfirmed">Requires Confirmation
                                            <input type="checkbox" name="isConfirmed" value="confirmed" />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="widget-content">
                                    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
                                    <button type="submit" class="btn btn-primary" tabindex="3">Add Schedule</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $page->endBody();
        echo $page->render('../includes/template.php');
    } else {
        Redirect::to('../schedule/');
    }

} else {
    Redirect::to('../login/');
}
