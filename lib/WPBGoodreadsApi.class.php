<?php



class WPBGoodreadsApi  {
    

    private function get( $Url ) {
        $response = wp_remote_get($Url, array( 'timeout' => '20' ));
        $body = wp_remote_retrieve_body($response);
        $body = json_encode( simplexml_load_string( $body ) );
        $body = json_decode( $body, true );
        return $body;
    }
    
    /**
     * @todo add defaulted params for user id, shelf
     * @global type $wpb_options 
     */
    public function getShelf( $Args = array() ) {
        global $wpb_options;
        
        if( !isset( $Args['shelf'] ) ) 
            $Args['shelf'] = get_option( 'bookshelf_shelf' );
        
        if( !isset( $Args['userid'] ) ) 
            $Args['id'] = get_option( 'bookshelf_userid' );
        
        if( !isset( $Args['per_page'] ) )
            $Args['per_page'] = get_option( 'bookshelf_book_limit' );
        
        $url = WPB_GOODREADS_API_ENTRY . 'review/list?format=xml&v=2';
        $url .= '&key=' . $this->getApiKey();
        $url .= '&id=' . $Args['id'];
        $url .= '&shelf=' . $Args['shelf'];
        $url .= '&per_page=' . $Args['per_page'];
        $shelf = $this->get( $url );
        $shelf = $shelf['reviews']['review'];
        
        return $shelf;
    }
    
    private function getApiKey() {
        return 'xXwyoFKBvkjIXMLiXxKw';
    }
} // end class