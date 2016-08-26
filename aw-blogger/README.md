#Aw Blogger

## Plugin Name: [AW Blogger](https://github.com/dev-artworld/plugins/tree/master/aw-blogger)
Plugin allow admin to create a new site using the XML configuration file and Template blog.
plugin setup a new wordpress blog on the server using the different parameters that are defined in the XML file.
plugin replicates all the setting from the template blog which is selected when creating the blog.

###Features
* [Create Blog using XML configuration file.](https://github.com/dev-artworld/plugins/tree/master/aw-blogger#add-new-blog-interface)
* [List of all the blog created.](https://github.com/dev-artworld/plugins/tree/master/aw-blogger#list-of-all-blogs)
* [Visit any blog directly form list.](https://github.com/dev-artworld/plugins/tree/master/aw-blogger#list-of-all-blogs)
* [Go directly to any blog's dashboard form list.](https://github.com/dev-artworld/plugins/tree/master/aw-blogger#list-of-all-blogs)
* [Delete blogs.](https://github.com/dev-artworld/plugins/tree/master/aw-blogger#list-of-all-blogs)
* Function to test if XML config file is valid.

<br />
### Add New Blog Interface
<br />

<p align="center">
  <img src="sample/aw-blogger.png" alt="" width="800"/>
</p>

<br />

### List of all blogs.
<br />

<p align="center">
  <img src="sample/aw-blogger-list.png" alt="" width="800"/>
</p>

<br />

### Sample XML configuration file structure.
```
<site>
	<address>[PathToCreateBlog]</address>
	<title>[BlogTitle]</title>
	<description>[Description]</description>
	<owner>[AdminEmail]</owner>
	<theme>[Theme]</theme>
	<feeds>
		<feed>
			[XmlFeedUrl]
		</feed>
	</feeds>
	<options>
		<option>
			[CustomOptions]
		</option>
	</options>
	<pages>
		<page>
			[Pages]
		</page>
	</pages>
</site>
```
