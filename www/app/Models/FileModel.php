<?php
class FileModel extends AppModel {

    public function getCss( string $path ) {
        return file_get_contents( $path );
    }

}
