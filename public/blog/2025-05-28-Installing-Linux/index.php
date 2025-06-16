<?php
require_once __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <p>The beginning of my homelab required me to install Linux on my chosen machine, the old Toshiba laptop. I had to pick a distro suitable for my needs which was something relatively light, robust, and familiar. As an Ubuntu user, I settled on Debian which is apparently a bit more minimal but still in the same family. I downloaded the Debian 12 ISO, mounted it onto my USB, and successfully managed to boot the laptop from the live USB:</p>

  <a href="./installer.jpg" target="_blank" class="image-link">
    <img src="./installer.jpg" alt="Debian installer" width="275">
  </a>

  <p>Then I went through the entire installation process and set everything up correctly as confirmed by the installer. However, I encountered an issue where Debian wouldn't boot whenever I tried. I did some research and discovered the rescue mode tool in the Debian installer, so I used that and did a bit more research to try and see if I could fix any potential issues from rescue mode.</p>

  <p>There was an option in rescue mode that prompted you to reinstall GRUB so I tried that several times but still nothing. The laptop was definitely booting in UEFI mode since I checked in the BIOS, so why the laptop wasn't finding the GRUB bootloader was really strange. I just kept getting a bunch of error messages over and over from the firmware, indicating that no boot entries were being found:</p>

  <a href="./boot-fail.jpg" target="_blank" class="image-link">
    <img src="./boot-fail.jpg" width="275" alt="Boot errors">
  </a>

  <p>Then I mounted and launched a shell inside the root partition <code>/dev/sda2</code> which was another option in rescue mode. ChatGPT suggested installing GRUB whilst in the shell or copying GRUB to the fallback EFI boot location, so I tried those. I had to mount the system directories in a specific way in order for me to <code>chroot</code> into it.</p>

  <p>That required the following commands:</p>

  <?php
  $code = <<<BASH
mount -t proc /proc /proc
mount --rbind /sys /sys
mount --rbind /dev /dev
mount --make-rslave /sys
mount --make-rslave /dev
chroot .
BASH;
  require alias("@code");
  ?>

  <p>After <code>chroot</code>'ing, I tried installing GRUB manually in the shell but it didn't work so I tried copying the .efi file to the fallback boot location and then rebooting:</p>

  <?php
  $code = <<<BASH
mkdir -p /boot/efi/EFI/BOOT
cp /boot/efi/EFI/debian/grubx64.efi /boot/efi/EFI/BOOT/BOOTX64.EFI
BASH;
  require alias("@code");
  ?>

  <p>And it worked! Not the most ideal solution but as a workaround, it was ok. I managed to get GRUB to appear and successfully booted into the system:</p>

  <a href="./success.jpg" target="_blank" class="image-link">
    <img src="./success.jpg" width="275" alt="Logged in">
  </a>

  <p>After that I just installed <code>sudo</code> to allow me to run commands with root permissions. But I was so glad when I finally got the machine to boot, at that point I had been debugging for a couple of hours. It's strange though because after I booted successfully, I verified that the <code>/boot/efi/EFI/debian</code> directory exists with the GRUB .efi file, which it does. I also verified that it is registered as a current boot entry with <code>efibootmgr</code>, but the system still boots from the fallback path. After some research I came to the conclusion that it's just something weird about the way the laptop's firmware is interacting with the boot sequence. But either way, it boots, that's all I care about. Now I have a machine ready to start homelabbing with which is huge.</p>
</section>
<?php
require alias("@tail");
