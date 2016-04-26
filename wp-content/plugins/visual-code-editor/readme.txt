=== Plugin Name ===
Contributors: Freelance Web Developer
Donate link: 
Tags: TinyMCE, visual editor, syntax highlighting, code snippets
Requires at least: 2.5
Tested up to: 2.7
Stable tag: 1.2.2

Visual Code Editor modifies WordPress's behavior so that code format is preserved when using the visual editor. It will work with any syntax highlighter that accepts encoded/escaped syntax.

== Description ==

Visual Code Editor modifies WordPress's behavior so that code format is preserved when using the visual editor. It will work with any syntax highlighter that accepts encoded/escaped syntax.

* Adds `<pre>` & `<code>` to block format menu
* Allows extra attributes for compatibility in some syntax highlighters (ie, `<pre lang="php" line='5'>`)
* Unescape WP's double escaping of &amp;
* Allows iFrames in the post
* Support for syntax highlighting in comments


== Change History ==

* v1.2.2: Support for syntax highlighting in comments
* v1.2.1: Compatible with PHP4
* v1.2: Allows iFrames in the post
* v1.1: Removes extra `<pre>` tags around SyntaxHighllighter Plus's [sourcecode] blocks

== Installation ==

1. Download and decompress the zip.
2. Upload the `visual-code-editor` folder to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

None at this time.

== Usage ==

Assuming you are using the SyntaxHighlighter Plus plugin...

1. In visual mode, create a [sourcecode language="xxx"][/sourcecode] block
2. Select the sourcecode block
3. Select Preformatted from TinyMCE's format menu
4. Paste in your code

== Screenshots ==

1. Coupled with the SyntaxHighlighter Plus plugin, you get results like the above.
2. All you have to do is create a [sourcecode language="xxx"][/sourcecode] block, paste in your code, select the whole sourcecode block, then select Preformatted from Tiny's format menu. It should look like the above.