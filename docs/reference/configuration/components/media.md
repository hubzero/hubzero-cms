<!--
status: generated
source: core/components/com_media/config/config.xml
-->

# Media (com_media)

Parameters from [`core/components/com_media/config/config.xml`](../../../../core/components/com_media/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Component

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `upload_extensions` | Legal Extensions (File Types) | text | `bmp,BMP,csv,CSV,doc,DOC,docx,DOCX,epg,EPG,eps,EPS,gif,GIF,ico,ICO,jpg,JPG,jpeg,JPEG,key,KEY,keynote,KEYNOTE,mp4,MP4,mp3,MP3,m4a,M4A,m4v,M4V,odg,ODG,odp,ODP,ods,ODS,odt,ODT,pdf,PDF,png,PNG,ppt,PPT,pptx,PPTX,swf,SWF,txt,TXT,xcf,XCF,xls,XLS,xlsx,XLSX,svg,SVG` | Extensions (file types) you are allowed to upload (comma separated). |
| `upload_maxsize` | Maximum Size (in MB) | text | `10` | The maximum size for an upload (in megabytes). Use zero for no limit. Note: your server has a maximum limit. |
| `file_path` | Path to files folder | text | `images` | Enter the path to the files folder relative to root. Warning! Changing to another path than the default 'images' may break your links. |
| `image_path` | Path to images folder | text | `images` | Enter the path to the images folder relative to root. This path has to be the same as path to files (default) or to a subfolder of the path to file folder. |
| `restrict_uploads` | Restrict Uploads | radio | `1 (Yes)` | Restrict uploads for lower than manager users to just images if Fileinfo or MIME Magic isn't installed. Options: `0` No, `1` Yes. |
| `check_mime` | Check MIME Types | radio | `1 (Yes)` | Use MIME Magic or Fileinfo to attempt to verify files. Try disabling this if you get invalid mime type errors. Options: `0` No, `1` Yes. |
| `image_extensions` | Legal Image Extensions (File Types) | text | `bmp,gif,jpg,png` | Image Extensions (file types) you are allowed to upload (comma separated). These are used to check for valid image headers. |
| `ignore_extensions` | Ignored Extensions | text | — | Ignored file extensions for MIME type checking and restricted uploads |
| `upload_mime` | Legal MIME Types | text | `image/jpeg,image/gif,image/png,image/bmp,application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,text/plain,application/x-zip` | A comma separated list of legal MIME types for upload |
| `upload_mime_illegal` | Illegal MIME Types | text | `text/html` | A comma separated list of illegal MIME types for upload (blacklist) |
