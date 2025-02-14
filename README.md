# TC-C-CMS

Welcome to **TC-C-CMS** (Teleco's Crappy Content Management System)!
This project consists mostly of some loose and very basic code that helps you manage content for your websites and make them (to some degree) usable!

## Project "Structure"
(i included this because this is what "professionals" are supposed to do ... i am not a professional however)

### **adminier folder (no login system because that's your job)**
Contains the admin welcome panel, backup database function, and links to edit the database (**very very very fragile**).
Please, if you dare use this in production, make many database backups becaus I wrote this entire thing from the ground up myself.

It also contains a **file manager** that can back up the entire website folder along with the database, as well as upload and manage files (also written by me, poorly).

- **admin/**: Contains the admin panel for managing the CMS.
  - **tools/**: Includes various tools described above.
  - **index.php**: The main entry point for the admin panel.
  - **backup_databases.php**: Makes database backups and stuff.

### **Common rendering utilities**
Contains common things needed to render websites with beautiful PHP. Mmm yes, I love PHP ❤️❤️❤️. **JS bad!**

- **common/**: Shared resources and functions used across different websites.
  - **functions.php**: Common functions and variables used throughout the CMS.
  - **navbar.php**: Functions for rendering the standard PURE HTML NO JS navbar.
  - **navbarjs.php**: Functions for rendering the JavaScript kinda required navbar.
---

## **Included Website Templates**

### **Website 1**
- Sidebars only, no navbar, no JS at all.
- Semi-mobile optimized.
- **Template name:** *einfach geil*

- **website/**: Contains the public-facing code for the first template.
  - **public/**: The public directory for the first website.
    - **index.php**: The main entry point.

### **Website 2**
- Navbar only, no sidebars.
- You can mix and match stuff, but I haven't (yet).
- **Template name:** *fick geil jaman*

- **website2/**: Contains the public-facing code for the second template.
  - **public/**:
    - **index.php**: The main entry point.

### **Website 3**
- Sidebars only, no navbar.
- Some small, goofy JS (ew, I know).
- Has **cat virtual support**.
- **Template name:** *Ich hasse JS number 1 geh kotzen*

- **website3/**:
  - **public/**:
    - **index.php**

### **Website 4**
- Navbar only, no sidebars.
- Some small, goofy JS (ew, I know).
- Has **cat virtual support**.
- **Template name:** *Ich hasse JS nummer 2 flatterschiss*

- **website4/**:
  - **public/**:
    - **index.php**
---

## **Getting Started**

### **Cool, I don’t care. What can it do and how do I use it?**
You can make basic **goofy ahh sites** and dynamically change things from the admin panel.
This includes creating pages and posts, adding navbar/sidebar entries, and uploading files.
It's like... *wow, so super premium* (irony).

### **TL;DR**
It’s so simple to run that I could do it in my sleep, blindfolded, and without any sense of touch.
There are **4 basic website templates** in this repo, based on actual sites I run (or plan to run soon with this tool).
---

### **What do you need to get started?**
- Install SQLite and PHP on whatever potato PC you have lying around.
- How you install PHP on your potato machine? IDK. (Add description for Mac, Debian, and Linux but **omit Windows** because we hate Windows. If someone opens an issue asking how to run this on Windows, I will not answer it.)

Unlike normal CMSs that go *brrrrrr* with a million Composer dependencies, this one **doesn't**.
Honestly, you could even run this **without a web server** if you run two PHP dev servers and set folder permissions so that the user-exposed PHP dev server can’t access admin parts (but the admin one can).
I **do not endorse or recommend this**, it’s just a *"yeah, you can do this"* kind of thing.

### **Run it in 2 steps**
1. Run:
   `php -S localhost:8000`
   in the root directory.
2. Open in your browser:
   [http://localhost:8000/website4/public/](http://localhost:8000/website4/public/)

And *voilà*! You have a pretty website! 🎉
---

### **Editing Content**
If you want to edit its content:

1. Go to **[http://localhost:8000/admin/](http://localhost:8000/admin/)**
2. Click **edit database** to mess with it (and pray).

⚠ **Warning:** Don’t be shocked when you see **literally no CSS** after the initial admin panel.
This is a *design choice* I made because **f*** bloat and JS**.

I *could* explain how to do things in the admin panel, but it should be **self-explanatory** if you look at the database examples.

### **Navbar vs Sidebar**
There are **two main layouts**:
- **Navbar only** → Website 2 (pure HTML, no JS at all) and Website 4 (some JS, ewww).
- **Sidebars only** → Website 1 and Website 3.
---

## **Features**
- **Multi-site support**: Manage multiple websites from a single CMS.
- **Customizable navigation**: Easily configure and render navigation bars.
- **Admin panel**: Manage content, files, and databases via a purposely painful UI.
- **Responsive design**: Ensures your websites *kind of* work on mobile.
---

## **Contributing**
You **can** contribute if you want…
…but I doubt anyone will.

💀 **ashdoiasjhdkljahspdoisfopiud fpoisdamupfn oiseupnf98i** 💀  
---

## **License**
This project is licensed under the **MIT License**.
See the [LICENSE](LICENSE) file for details. 

Basically:  
- **No warranty.**
- **No responsibility.**
- **I barely know how to code.**
---

### **Made with love for PHP and pure hatred for JavaScript by T.B.** ❤️ 🚀
*ps if you want to know why theres no real commit history as of now 2025-02-14 its because of my tendency to swear in them and I wanted to not spoil this project too much with that yet...*