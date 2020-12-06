<?php

namespace common;

class Page {
    protected $_title;
    protected $_writers = [];
    protected $_wrote_pre = false;
    protected $_wrote_post = false;
    protected $_writing = false;
    protected $_scripts = [];
    protected $_styles = [];

    public function calls(callable $writer) {
        $this->_writers[] = $writer;

        if ( $this->_writing ) {
            $writer();
        }

        return $this;
    }

    public function writes($string) {
        $this->calls(function() use($string) {
            echo $string;
        });

        return $this;
    }

    public function script($src) {
        $this->_scripts[] = $src;
        return $this;
    }

    public function stylesheet($src) {
        $this->_styles[] = $src;
        return $this;
    }

    public function title($title = null) {
        if ( $title ) {
            $this->_title = $title;
            return $this;
        } else {
            return $this->_title;
        }
    }

    public function header($string = null) {
        if ( !$string ) $string = $this->title();

        return $this->writes('<h1>' . $string . '</h1>');
    }

    public function form($submit, callable $inner) {
        $this->writes('<form method="POST" action="' . $submit . '">');
        $this->calls($inner);
        $this->writes('</form>');
        return $this;
    }

    public function submit($text = 'Submit') {
        $this->writes('<button type="submit">' . $text . '</button>');
    }

    public function div(callable $inner) {
        $this->writes('<div class="common-page">');
        $this->calls($inner);
        $this->writes('</div>');
        return $this;
    }

    public function line_break() {
        $this->writes('<br/>');
        return $this;
    }

    public function table($nested_array) {
        if ( is_array($nested_array) ) {
            $this->calls(function() use($nested_array) {
                echo '<table style="width: 100%;">';
    
                foreach ( $nested_array as $row_idx => $row ) {
                    echo '<tr>';
    
                    foreach ( $row as $col_idx => $col ) {
                        $tag = $row_idx === 0 ? 'th' : 'td';
    
                        echo "<{$tag}>{$col}</{$tag}>";
                    }
    
                    echo '</tr>';
                }
    
                echo '</table>';
            });
        } else {
            $this->writes('<table style="width: 100%;">');
            $this->calls($nested_array);
            $this->writes('</table>');
        }

        return $this;
    }

    public function table_row_cell(callable $inner, $span = 1) {
        $this->table_row(function() use($inner, $span) {
            $this->writes('<td colspan="' . $span . '">');
            $this->calls($inner);
            $this->writes('</td>');
        });

        return $this;
    }

    public function table_row($inner = null) {
        $this->writes('<tr>');
        if ( $inner ) $this->calls($inner);
        $this->writes('</tr>');

        return $this;
    }

    public function table_head(callable $inner) {
        $this->writes('<th>');
        $this->calls($inner);
        $this->writes('</th>');

        return $this;
    }

    public function table_cell($inner = null) {
        $this->writes('<td>');
        if ( $inner ) $this->calls($inner);
        $this->writes('</td>');

        return $this;
    }

    public function fail_to($message, $redirect_url) {
        $this->writes('<p>' . $message . '</p>')
            ->writes('<p><a href="' . $redirect_url . '">Try Again</a></p>')
            ->write();

        exit;
    }

    public function preamble() {
        if ( $this->_wrote_pre ) return;
        $this->_styles[] = system_url('assets/common.css');

        $styles = [];
        foreach ( $this->_styles as $style ) {
            $styles[] = '<link rel="stylesheet" href="' . $style . '"/>';
        }

        ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?= $this->_title ?></title>
        <?= implode("\n", $styles) ?>
    </head>
    <body>
        <?php

        $this->_wrote_pre = true;
    }

    public function postamble() {
        if ( $this->_wrote_post ) return;

        $scripts = [];
        foreach ( $this->_scripts as $script ) {
            $scripts[] = '<script src="' . $script . '"></script>';
        }

        ?>
        <?= implode("\n", $scripts) ?>
    </body>
</html>
        <?php

        $this->_wrote_post = true;
    }

    public function write() {
        $this->_writing = true;
        $this->preamble();
        foreach ( $this->_writers as $writer ) {
            $writer();
        }
        $this->postamble();
        $this->_writing = false;
    }

    public function compile() {
        ob_start();
        $this->_wrote_pre = false;
        $this->_wrote_post = false;
        $this->write();
        $this->_wrote_pre = false;
        $this->_wrote_post = false;
        return ob_get_clean();
    }
}
