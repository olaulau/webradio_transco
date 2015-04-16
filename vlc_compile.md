# how to compile VLC
needed for ubuntu 12.04, into which VLC may fail to transcode  
no solution with FFmpeg/libav war which lacks FFserver, which could have been an alternative to VLC


### VLC version
use 2.1.6 for ubuntu 12.04, the last which compiles without any dependancy problem.  
on recent distro, you can compile more recent (2.2.x) version this way, but you should use the one provided in your repository.


### prepare your system (root commands)
add deb-src lines to your source.list  
- apt-get update  
- apt-get install libjack-jackd2-dev  
- apt-get build-dep vlc

note installed packages for later (exemple) :  
Les NOUVEAUX paquets suivants seront installés :
  autopoint debhelper dh-apparmor dh-autoreconf dh-buildinfo gir1.2-atk-1.0 gir1.2-freedesktop
  gir1.2-gdkpixbuf-2.0 gir1.2-gtk-2.0 gir1.2-notify-0.7 gir1.2-pango-1.0 gir1.2-rsvg-2.0 html2text
  intltool-debian liba52-0.7.4-dev libaa1 libaa1-dev libass-dev libatk1.0-dev libaudio2
  libavc1394-dev libavcodec-dev libavformat-dev libavutil-dev libbluray-dev
  libcairo-script-interpreter2 libcairo2-dev libcddb2-dev libcdio-dev libcrystalhd-dev
  libdc1394-22-dev libdca-dev libdirac-decoder0 libdirac-dev libdirectfb-dev libdirectfb-extra
  libdvbpsi-dev libdvdnav-dev libdvdread-dev libebml-dev libenca-dev libfaad-dev libflac-dev
  libfluidsynth-dev libfluidsynth1 libfontconfig1-dev libfribidi-dev libgdk-pixbuf2.0-dev
  libgtk2.0-dev libiso9660-dev libjack-dev libjack0 libkate-dev liblircclient-dev liblivemedia-dev
  liblua5.1-0-dev libmad0-dev libmatroska-dev libmng1 libmodplug-dev libmpcdec-dev libmpeg2-4-dev
  libmtp-dev libncursesw5-dev libnotify-dev libnotify4 libomxil-bellagio-bin libomxil-bellagio-dev
  libomxil-bellagio0 liborc-0.4-dev libpango1.0-dev libpixman-1-dev libpostproc-dev libproxy-dev
  libqt4-declarative libqt4-designer libqt4-dev libqt4-help libqt4-qt3support libqt4-script
  libqt4-scripttools libqt4-svg libqt4-test libqt4-xmlpatterns libqtgui4 libraw1394-dev
  libreadline-dev libreadline6-dev libresid-builder-dev librsvg2-bin librsvg2-dev
  libsamplerate0-dev libschroedinger-dev libsdl-image1.2 libsdl-image1.2-dev libshout3-dev
  libsidplay2-dev libsmbclient-dev libsndfile1-dev libspeex-dev libspeexdsp-dev libsvga1
  libsvga1-dev libswscale-dev libsysfs-dev libtag1-dev libtar-dev libtar0 libtiff4-dev
  libtiffxx0c2 libtwolame-dev libudev-dev libupnp-dev libupnp3-dev libusb-1.0-0-dev libv4l-dev
  libva-dev libva-egl1 libva-glx1 libva-tpi1 libva-x11-1 libvcdinfo-dev libx11-xcb-dev libx264-dev
  libx86-1 libxcb-composite0 libxcb-composite0-dev libxcb-keysyms1 libxcb-keysyms1-dev
  libxcb-randr0 libxcb-randr0-dev libxcb-render0-dev libxcb-shape0-dev libxcb-shm0-dev
  libxcb-xfixes0 libxcb-xfixes0-dev libxcb-xv0 libxcb-xv0-dev libxcomposite-dev libxcursor-dev
  libxdamage-dev libxft-dev libxml2-dev libxpm-dev libzvbi-dev lua5.1 po-debconf
  qt4-linguist-tools qt4-qmake x11proto-composite-dev x11proto-damage-dev


### download and compile
- wget http://get.videolan.org/vlc/2.1.6/vlc-2.1.6.tar.xz
- tar -xvf vlc-2.1.6.tar.xz
- cd vlc-2.1.6
- ./configure
- ./compile -j 2  


### test
./vlc


### clean (root commands)
- apt-get remove (tous les paquets installés par le build-dep, penser à supprimer les sauts de ligne)  
remove deb-src lines if not necessary  
- apt-get update  

