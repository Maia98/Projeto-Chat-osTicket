<h1 style="text-align:center;"><?php echo __('Manage Your Profile Information'); ?></h1>
<p><?php echo __(
'Use the forms below to update the information we have on file for your account'
); ?>
</p>
<form action="profile.php" method="post">
    <?php csrf_token(); ?>
    <div class="row">
        <div class="col-md-12">
          <?php
            foreach ($user->getForms() as $f) {
                $f->render(false);
            }
            if ($acct = $thisclient->getAccount()) {
                $info=$acct->getInfo();
                $info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
          ?>
          <?php } ?>
        </div>

        <div class="col-md-12">
            <hr />
            <div>
              <h3><?php echo __('Preferences'); ?></h3>
            </div>
            <div class="row">
              <div class="col-md-6">
                  <label><?php echo __('Time Zone'); ?>:</label>
                  <select class="form-control" name="timezone_id" id="timezone_id">
                      <option value="0">&mdash; <?php echo __('Select Time Zone'); ?> &mdash;</option>
                      <?php
                      $sql='SELECT id, offset,timezone FROM '.TIMEZONE_TABLE.' ORDER BY id';
                      if(($res=db_query($sql)) && db_num_rows($res)){
                          while(list($id,$offset, $tz)=db_fetch_row($res)){
                              $sel=($info['timezone_id']==$id)?'selected="selected"':'';
                              echo sprintf('<option value="%d" %s>GMT %s - %s</option>',$id,$sel,$offset,$tz);
                          }
                      }
                      ?>
                  </select>
                  &nbsp;<span class="error"><?php echo $errors['timezone_id']; ?></span>
              </div>
              <div class="col-md-9">
                  <label><?php echo __('Daylight Saving') ?>: </label>
                  <input type="checkbox" name="dst" value="1" <?php echo $info['dst']?'checked="checked"':''; ?>>
                  <?php echo __('Observe daylight saving'); ?>
                  <em>(<?php echo __('Current Time'); ?>:
                  <strong><?php echo Format::date($cfg->getDateTimeFormat(),Misc::gmtime(),$info['tz_offset'],$info['dst']); ?></strong>)</em>
              </div>
              <div class="col-md-6">
                  <label><?php echo __('Preferred Language'); ?>: </label>

                  <?php
                    $langs = Internationalization::availableLanguages(); ?>
                    <select class="form-control" name="lang">
                        <option value="">&mdash; <?php echo __('Use Browser Preference'); ?> &mdash;</option>
                        <?php foreach($langs as $l) {
                        $selected = ($info['lang'] == $l['code']) ? 'selected="selected"' : ''; ?>
                                        <option value="<?php echo $l['code']; ?>" <?php echo $selected;
                                            ?>><?php echo Internationalization::getLanguageDescription($l['code']); ?></option>
                      <?php } ?>
                  </select>
                  <span class="error">&nbsp;<?php echo $errors['lang']; ?></span>

              </div>
            </div>
        </div>
        <div class="col-md-12">
            <?php if ($acct->isPasswdResetEnabled()) { ?>
                <hr>
                <h3><?php echo __('Access Credentials'); ?></h3>
                <div class="col-md-4">
                    <?php if (!isset($_SESSION['_client']['reset-token'])) { ?>
                            <label> <?php echo __('Current Password'); ?>: </label>
                            <input class="form-control" type="password" name="cpasswd" value="<?php echo $info['cpasswd']; ?>">
                            &nbsp;<span class="error">&nbsp;<?php echo $errors['cpasswd']; ?></span>
                    <?php } ?>
                </div>
                <div class="col-md-4">
                    <label><?php echo __('New Password'); ?>: </label>
                    <input class="form-control" type="password" name="passwd1" value="<?php echo $info['passwd1']; ?>">
                    &nbsp;<span class="error">&nbsp;<?php echo $errors['passwd1']; ?></span>
                </div>
                <div class="col-md-4">
                    <label><?php echo __('Confirm New Password'); ?>: </label>

                    <input class="form-control" type="password" name="passwd2" value="<?php echo $info['passwd2']; ?>">
                    &nbsp;<span class="error">&nbsp;<?php echo $errors['passwd2']; ?></span>
                </div>
            <?php } ?>
        </div>
    </div>
    <hr />
    <div style="text-align: center;">
      <input class="btn btn-success" type="submit" value="Update"/>
      <input class="btn btn-warning" type="reset" value="Reset"/>
      <input class="btn btn-danger" type="button" value="Cancel" onclick="javascript: window.location.href='index.php';"/>
    </div>
</form>
