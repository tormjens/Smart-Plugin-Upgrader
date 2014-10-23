<?php  
/**
* Smart Plugin/Theme Upgrader
* 
* Sometimes the native plugin/theme upgrader does not work. 
* This class solved many problems for us.
* 
* @author Tor Morten Jensen <tormorten@smartmedia.no>
*/
class Smart_Upgrader {

	/**
	 * The download URL
	 *
	 * @var string
	 **/
	public $url = '';

	/**
	 * The plugin slug
	 *
	 * @var string
	 **/
	public $slug = '';

	/**
	 * The path where zip file is being saved
	 *
	 * @var string
	 **/
	public $file_path = '';

	/**
	 * The path to the directory of the plugin
	 *
	 * @var string
	 **/
	public $dir_path = '';

	/**
	 * The plugin name (for WordPress)
	 *
	 * @var string
	 **/
	public $plugin = '';
	
	/**
	 * Sets the variables to be used
	 *
	 * @return void
	 **/
	public function __construct( $url, $slug ) {

		$this->url = $url;
		$this->slug = $slug;
		$this->file_path = WP_CONTENT_DIR . '/plugins/' . $slug . '.zip';
		$this->dir_path = WP_CONTENT_DIR . '/plugins/' . $slug;
		$this->plugin = $slug . '/' . $slug . '.php';
		
	}

	/**
	 * Installs the current instance
	 *
	 * @return boolean Depending of the result
	 **/
	public function install() {

		if( $this->download() ) {

			if( $this->unpack() ) {

				return true;

			}

		}

		return false;

	}

	/**
	 * Upgrades the current instance
	 * 
	 * Will delete the old folder, download a new zip file and unzip it
	 *
	 * @return boolean Depending of the result
	 **/
	public function upgrade() {

		if( is_dir( $this->dir_path ) ) {
			if( $this->rmdir() ) {

				return $this->install();

			}
		}

		return false;

	}

	/**
	 * Downloads the instances zip file from the internets
	 *
	 * @return boolean Depending of the result
	 **/
	private function download() {

		$ch = curl_init($this->url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    if(file_put_contents($this->file_path, $data))
	            return true;
	    else
	            return false;

	}

	/**
	 * Unpacks the instances zip file, creates the correct dir and cleans up
	 *
	 * @return boolean Depending of the result
	 **/
	private function unpack() {

		if( ! is_dir( $this->dir_path ) ) {
			if( file_exists( $this->dir_path ) ) {
				unlink( $this->dir_path );
			}
			$create = wp_mkdir_p( $this->dir_path );
			if( ! $create ) {
		    	$this->clean();
				return false;
			}
		}

		$zip = new ZipArchive;
		if ( $zip->open( $this->file_path ) === TRUE) {
		    $zip->extractTo( $this->dir_path );
		    $zip->close();
		    $this->clean();
		    return true;
		} 

		return false;
	    
	}

	/**
	 * Cleans up instances temporary files
	 *
	 * @return void
	 **/
	private function clean() {

		unlink( $this->file_path );

	}


	/**
	 * Activates the current instance, specific for plugins.
	 *
	 * @return boolean Depending of the result
	 **/
	public function activate() {
		$current = get_option('active_plugins');
	    $plugin = $this->plugin;

	    if(!in_array($plugin, $current)) {
	            $current[] = $plugin;
	            sort($current);
	            do_action('activate_plugin', trim($plugin));
	            update_option('active_plugins', $current);
	            do_action('activate_'.trim($plugin));
	            do_action('activated_plugin', trim($plugin));
	            return true;
	    }
	    else 
    		return false;
	}

	/**
	 * Recursive deletes the current instances directory
	 *
	 * @return boolean Depending of the result
	 **/

	private function rmdir() {

		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
				} 
			} 
			reset($objects); 
			rmdir($dir); 
			return true;
		} 

		return false;

	}
}

/**
* Theme Upgrader
* 
* Uses most of the same functionality as the plugin upgrader
* 
* @author Tor Morten Jensen <tormorten@smartmedia.no>
*/
class Smart_Upgrader_Theme extends Smart_Upgrader {
	
	/**
	 * Sets the variables to be used
	 *
	 * @return void
	 **/
	public function __construct( $url, $slug ) {
		
		$this->url = $url;
		$this->slug = $slug;
		$this->file_path = WP_CONTENT_DIR . '/themes/' . $slug . '.zip';
		$this->dir_path = WP_CONTENT_DIR . '/themes/' . $slug . '/';
		$this->plugin = $slug;

	}

	/**
	 * Activates the current instance, specific to themes.
	 *
	 * @return boolean Depending of the result
	 **/
	public function activate() {
	    $plugin = $this->plugin;

	    switch_theme( $plugin );

	    return true;
	}
}

?>