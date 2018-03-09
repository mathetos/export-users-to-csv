# Export Users to CSV
A WordPress plugin that exports user data and meta data. You can even export the users by role and registration date range. 

By default, it does not export user passwords as a security concern. See the FAQ for how to include them regardless.

###Screenshots
![The User export screen](screenshot-1.gif)
_The User export screen_

![The User export button](screenshot-2.png)
_The User export button_

###FAQ
**How do I include user passwords in the export?**
I don’t really recommend it since storing passwords in plain-text can be a real liability issue. Nevertheless, you can add this filter to your site to allow the password to be included as a column in your CSV file:

```
add_filter('pp_eu_exclude_data', 'my_prefix_include_password');

function my_prefix_include_password() {
     return array();
}
```
