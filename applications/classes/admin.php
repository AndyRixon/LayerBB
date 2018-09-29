<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

/*
 * Admin class of LayerBB
 */
if (!defined('BASEPATH')) {
    die();
}

class LAYER_Admin
{

    private $links = array();

    public function __construct()
    {
        //Adding default navigation for ACP.
        $this->addNav(
            'Configuration',
            array(
                'System Settings' => SITE_URL . '/admin/general.php',
                'Manage Applications' => SITE_URL . '/admin/applications.php',
                'Manage Themes' => SITE_URL . '/admin/theme.php',
                'Manage Navbar' => SITE_URL . '/admin/navbar.php',
                'Manage Sidebar' => SITE_URL . '/admin/sidebar.php'
            )
        );
        $this->addNav(
            'User Management',
            array(
                'Manage Usergroups' => SITE_URL . '/admin/usergroups.php',
                'Manage Users' => SITE_URL . '/admin/members.php',
                'Custom Profile Fields' => SITE_URL . '/admin/profile_fields.php',
                'Mass Email Users' => SITE_URL . '/admin/massemail.php'

            )
        );
        $this->addNav(
            'Forum Management',
            array(
                'Manage Categories' => SITE_URL . '/admin/manage_category.php',
                'Manage Nodes' => SITE_URL . '/admin/manage_node.php'
            )
        );
    }

    /*
     * Function for adding a navigation link in the ACP
     */
    public function addNav($name, $links = array())
    {
        $this->links[$name] = array(
            'name' => $name,
            'links' => array()
        );
        foreach ($links as $value => $href) {
            $this->links[$name]['links'][] = array(
                'value' => $value,
                'href' => $href
            );
        }
    }

    /*
     * Adding a content box in ACP.
     */
    public function box($header = null, $content, $table = "", $column = "6")
    {
        $columns = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12');
        $column = (in_array($column, $columns)) ? $column : '6';
        $header = ($header == null) ? '' :  $header;
        $return = '<section class="col-lg-' . $column . '">
<div class="box box-success">
            <div class="box-header">
              <i class="fa fa-cogs"></i>

              <h3 class="box-title">' . $header . '</h3>
                              <div class="box-body">
                                ' . $content . '
                              </div>
                              ' . $table . '
                          </div>
                      </div></section>';
        return $return;
    }

    /*
     * Notification.
     */
    public function alert($content, $type = "info")
    {
        $types = array('success', 'info', 'warning', 'danger');
        $type = (in_array($type, $types)) ? $type : 'info';
        return '<div class="alert alert-' . $type . '">' . $content . '</div>';
    }

    /*
     * Display the ACP navigation.
     */
    public function navigation()
    {
        $return = '';
        foreach ($this->links as $link) {
            $return .= '<li class="treeview">
          <a href="#">
            <i class="fa fa-bars"></i> <span>' . $link['name'] . '</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">';
            foreach ($link['links'] as $page) {
                $return .= '<li><a href="' . $page['href'] . '"><i class="fa fa-circle-o"></i> ' . $page['value'] . '</a></li>';
            }

            $return .= '</ul>
        </li>';
        }
        return $return;
    }

    public function applications()
    {
        global $MYSQL, $LAYER;
        $return = '';
        $return .= '<li class="treeview">
          <a href="#">
            <i class="fa fa-server"></i> <span>Applications</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">';
        $query = $MYSQL->query("SELECT * FROM {prefix}apps WHERE active = 1");
         if(!empty($query)) {
            foreach ($query as $app) {
                $return .= '<li><a href="' . SITE_URL . '/admin/' . $app['appid'] . '.php"><i class="fa fa-circle-o"></i> ' . $app['title'] . '</a></li>';
            }
        } else {
            $return .= '<li><a href="' . SITE_URL . '/admin/applications.php"><i class="fa fa-circle-o"></i> No Apps Installed</a></li>';
        }

            $return .= '</ul>
        </li>';
        return $return;
    }

    public function zip_extract($file_input, $report = false, $update = false)
    {
        $zipHandle = zip_open($file_input);
        $i = 0;
        $file_message = array();
        $error = array();
        var_dump($zipHandle);
        while ($file = zip_read($zipHandle)) {
            $i++;
            $thisFileName = zip_entry_name($file);
            $thisFileDir = dirname($thisFileName);

            if (!zip_entry_open($zipHandle, $file, 'r')) {
                $error[$i] = 'File could not be handled: ' . $thisFileName . '<br />';
                continue;
            }
            if (!is_dir($thisFileDir)) {
                $file_message[$i] = '<li>' . $thisFileDir . ': ';
                mkdir($thisFileDir, 0755);
            }
            $zip_filesize = zip_entry_filesize($file);
            if (empty($zip_filesize)) {
                if (substr($thisFileName, -1) == '/') {
                    $file_message[$i] = '<li>' . $thisFileName . ': ';
                    if (!is_dir('../' . $thisFileName)) {
                        mkdir('../' . $thisFileName, 0755);
                    }

                    unset($thisFileDir);
                    unset($thisFileName);
                    continue;
                }
            }
            $content = zip_entry_read($file, $zip_filesize);

            if ($thisFileName == 'upgrade.php' && $update === true) {
                $file_message[$i] = '<li>' . $thisFileName . ': ';
                if (@file_put_contents('updates/' . $thisFileName, $content) === false) {
                    $error[$i] = 'File could not be handled: ' . $thisFileName . '<br />';
                }
            } else {
                $file_message[$i] = '<li>' . $thisFileName . ': ';
                if (@file_put_contents('../' . $thisFileName, $content) === false) {
                    $error[$i] = '#2 File could not be handled: ' . $thisFileName . '<br />';
                }
            }
            zip_entry_close($file);
            unset($thisFileDir);
            unset($thisFileName);
        }
        zip_close($zipHandle);
        if ($report === true) {
            $output = '<ul>';
            foreach ($file_message as $i => $message) {
                $output .= $message;
                if (@$error[$i] == '') {
                    $output .= '-> Done';
                } else {
                    $output .= $error[$i];
                }
                $output .= '</li>';
            }
            $output .= '</ul>';
        }

        return $output;
    }

    public function download($link, $update = false)
    {
        $file_name = basename($link);
        if (@fopen($link, 'r')) {
            if ($update === true && !is_file('updates/' . $file_name)) {
                $file = curl_init($link);
                if (!is_dir('updates/')) mkdir('updates/');
                $dlHandler = fopen('updates/' . $file_name, 'w');
                curl_setopt($file, CURLOPT_FILE, $dlHandler);
                curl_setopt($file, CURLOPT_TIMEOUT, 3600);
                curl_exec($file);
                fclose($dlHandler);
            } elseif (!is_file('downloads/' . $file_name)) {
                $file = curl_init($link);
                if (!is_dir('downloads/')) mkdir('downloads/');
                $dlHandler = fopen('downloads/' . $file_name, 'w');
                curl_setopt($file, CURLOPT_FILE, $dlHandler);
                curl_setopt($file, CURLOPT_TIMEOUT, 3600);
                curl_exec($file);
                fclose($dlHandler);
            }
            return $file_name;
        } else {
            return false;
        }
    }

    public function template($type) {
        global $LAYER;

        $data = ($LAYER->data['flat_ui_admin'] == 1)? 'template/old_' . $type . '.php' : 'template/' . $type . '.php';
        $return = '';
        ob_start();
        include($data);
        $return .= ob_get_contents();
        ob_end_clean();
        return $return;
    }

}

?>
