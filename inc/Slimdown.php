<?php
/**
 * Slimdown.php
 */
/**
 * Slimdown - A very basic regex-based Markdown parser. Supports the
 * following elements (and can be extended via Slimdown::add_rule()):
 *
 * - Headers
 * - Links
 * - Bold
 * - Emphasis
 * - Deletions
 * - Quotes
 * - Inline code
 * - Blockquotes
 * - Ordered/unordered lists
 * - Horizontal rules
 *
 * Author: Johnny Broadway <johnny@johnnybroadway.com>
 * Website: https://gist.github.com/jbroadway/2836900
 * License: MIT
 * 
 * Modified by RundesBalli.
 */
class Slimdown {
  public static $rules = array (
    '/(#+)(.*)/' => 'self::header',                                                              // headers
    '/\[([^\[]+)\]\(([^\)]+)\)\*/' => '<a href=\'\2\' target=\'blank\' rel=\'noopener\'>\1</a>', // links in blank tab
    '/\[([^\[]+)\]\(([^\)]+)\)/' => '<a href=\'\2\' rel=\'noopener\'>\1</a>',                    // links
    '/\[([\w-\.]+)\]\[(.+)\]/' => '<img src=\'/img/\1.png\' title=\'\2\' alt=\'\2\'>',           // internal image
    '/(\*\*|__)(.*?)\1/' => '<strong>\2</strong>',                                               // bold
    '/(\*|_)(.*?)\1/' => '<em>\2</em>',                                                          // emphasis
    '/\~\~(.*?)\~\~/' => '<s>\1</s>',                                                            // del
    '/`(.*?)`/' => '<code>\1</code>',                                                            // inline code
    '/\n\*(.*)/' => 'self::ul_list',                                                             // ul lists
    '/\n[0-9]+\.(.*)/' => 'self::ol_list',                                                       // ol lists
    '/\n([^\n]+)\n/' => 'self::para',                                                            // add paragraphs
    '/<\/ul>\s?<ul>/' => '',                                                                     // fix extra ul
    '/<\/ol>\s?<ol>/' => ''                                                                      // fix extra ol
  );

  private static function para ($regs) {
    $line = $regs[1];
    $trimmed = trim ($line);
    if (preg_match ('/^<\/?(ul|ol|li|h|p|bl)/', $trimmed)) {
      return "\n" . $line . "\n";
    }
    return sprintf ("\n<p>%s</p>\n", $trimmed);
  }
  
  private static function ul_list ($regs) {
    $item = $regs[1];
    return sprintf ("\n<ul>\n\t<li>%s</li>\n</ul>", trim ($item));
  }

  private static function ol_list ($regs) {
    $item = $regs[1];
    return sprintf ("\n<ol>\n\t<li>%s</li>\n</ol>", trim ($item));
  }

  private static function header ($regs) {
    list ($tmp, $chars, $header) = $regs;
    $level = strlen ($chars);
    return sprintf ('<h%d>%s</h%d>', $level, trim ($header), $level);
  }

  /**
   * Render some Markdown into HTML.
   */
  public static function render ($text) {
    $text = "\n" . $text . "\n";
    foreach (self::$rules as $regex => $replacement) {
      if (is_callable ( $replacement)) {
        $text = preg_replace_callback ($regex, $replacement, $text);
      } else {
        $text = preg_replace ($regex, $replacement, $text);
      }
    }
    return trim ($text);
  }
}

/**
 * Slimdown für eine Zeile
 */
class SlimdownOneline {
  public static $rules = array (
    '/\[([^\[]+)\]\(([^\)]+)\)\*/' => '<a href=\'\2\' target=\'blank\' rel=\'noopener\'>\1</a>', // links in blank tab
    '/\[([^\[]+)\]\(([^\)]+)\)/' => '<a href=\'\2\' rel=\'noopener\'>\1</a>',                    // links
    '/(\*\*|__)(.*?)\1/' => '<strong>\2</strong>',                                               // bold
    '/(\*|_)(.*?)\1/' => '<em>\2</em>',                                                          // emphasis
    '/\~\~(.*?)\~\~/' => '<s>\1</s>'                                                             // del
  );

  /**
   * Render some Markdown into HTML.
   */
  public static function render ($text) {
    $text = "\n" . $text . "\n";
    foreach (self::$rules as $regex => $replacement) {
      if (is_callable ( $replacement)) {
        $text = preg_replace_callback ($regex, $replacement, $text);
      } else {
        $text = preg_replace ($regex, $replacement, $text);
      }
    }
    return trim ($text);
  }
}