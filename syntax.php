<?php
/**
 * Plugin Now: Inserts a timestamp.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     David Peplow <david@peplow.net>
 */
 
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();
 

class syntax_plugin_simplewikipagetree extends DokuWiki_Syntax_Plugin {
    public function getType() { return 'substition'; }
    public function getSort() { return 32; }
 
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<simplewiki-pagetree>',$mode,'plugin_simplewikipagetree');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler) {
        return array($match, $state, $pos);
    }
 
    public function render($mode, Doku_Renderer $renderer, $data) {
    // $data is what the function handle return'ed.
        if($mode == 'xhtml'){
            /** @var Doku_Renderer_xhtml $renderer */
            $renderer->doc = '';
            $renderer->doc .= '<style>';
            $renderer->doc .= '.simplewiki-pagetree .open > .icon { background-image:url('.DOKU_REL.'lib/plugins/simplewikipagetree/img/chevron-down.svg);}';
            $renderer->doc .= '.simplewiki-pagetree .closed > .icon { background-image:url('.DOKU_REL.'lib/plugins/simplewikipagetree/img/chevron-right.svg);}';
            $renderer->doc .= '.simplewiki-pagetree .file > .icon { background-image:url('.DOKU_REL.'lib/plugins/simplewikipagetree/img/bullet.svg);}';
            $renderer->doc .= '</style>';
            
            $renderer->doc .= $this::renderPageTree('data/pages',true);
            return true;
        }
        return false;
    }

    private function renderPageTree($path, $root=false){
        global $INFO;
        $out .= '<ul';
        if($root == true){
            $out .= ' class="simplewiki-pagetree" ';
        }
        $out .= '>';
        $files = scandir($path);
        foreach($files as $file){
            
            if($file == '.' or $file == '..' or $file == 'start.txt' or $file == 'sidebar.txt'){
                continue;
            }

            $filepath = $path.'/'.$file;
            $id = $this->getID($filepath);

            if(is_dir($filepath)){
                if($id == substr($INFO['id'],0,strlen($id))){
                    $out .= '<li class="open">';
                }else{
                    $out .= '<li class="closed">';                    
                }
                           
            }else{
                $out .= '<li class="file">';
            }
            $out .= '<div class="icon"></div>'; 

            $activeInjection = '';
            if($this->isActive($filepath)){
                $activeInjection = ' class="active" ';
            }


            $out .= '<a href="'.$this->getURL($filepath).'"'.$activeInjection.'>';  
            $out .= $this->getTitle($filepath);
            $out .= '</a>';
            if(is_dir($filepath)){
                $out .= $this->renderPageTree($filepath);
            }

            $out .= '</li>';
        }
        $out .= '</ul>';
        return $out;
    }

    private function getID($filepath){
        $id = str_replace('data/pages/','',$filepath);
        $id = str_replace('/',':',$id);
        $id = str_replace('.txt','',$id);
        return $id;
    }

    private function getTitle($filepath){
        $id = $this->getID($filepath);

        if(is_dir($filepath)){
            $id .= ':start';
        }

        $title = p_get_first_heading($id);

        if($title == ''){
            $title = end(explode(':',$id));
        }

        return $title;
    }

    private function getURL($filepath){
        global $conf;
        $url = $this->getID($filepath);
        if(is_dir($filepath)){
            $url .= ':start';
        }
        if($conf['useslash'] == 1){
            $url = str_replace(":","/",$url);
        }
        $url = DOKU_REL.$url;
        return $url;
    }

    private function isActive($filepath){
        global $INFO;

        $id = $this->getID($filepath);
        if(is_dir($filepath)){
            $id .= ':start';
        }
        if($id == $INFO['id'] ){
            return true;
        }

        return false;
    }
}