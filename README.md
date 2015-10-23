# SimpleConfig #

Simple hook based config system using with a cached backend

Requires 
 - [Ethereal/SimpleCache](https://github.com/mathus13/SimpleCache)
 - [Ethereal/SimpleHooks](https://github.com/mathus13/SimpleHooks)

For uncreased usability and de-cuppleing these requirements will be reduced to an interface

At construction, hooks are used to add config elements. After that you can use the ->set method, passing the key, and value.

 To add items for on load, add a listener to the hook '\Ethereal\Config\Build' and append your key to the array passed in. 

####Example####

    class Listener 
    {
    	public function configListener($config)
    	{
    		$config['key'] = array(
				'subKey' => 'value'
    		);
    		return $config;
    	}
    }