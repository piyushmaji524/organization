-- ============================================================
-- Sarak Youth Development Council — Seed Data v1.0
-- Run AFTER schema.sql
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- roles (9 roles)
-- --------------------------------------------------------
INSERT INTO `roles` (`id`, `role_key`, `display_name`) VALUES
(1, 'super_admin',    'Super Admin'),
(2, 'president',      'President'),
(3, 'vp',             'Vice President'),
(4, 'treasurer',      'Treasurer'),
(5, 'secretary',      'Secretary'),
(6, 'asst_secretary', 'Assistant Secretary'),
(7, 'it_head',        'IT Head'),
(8, 'event_manager',  'Event Manager'),
(9, 'sm_manager',     'Social Media Manager');

-- --------------------------------------------------------
-- admins (1 Super Admin — default password: password)
-- --------------------------------------------------------
INSERT INTO `admins` (`id`, `username`, `password_hash`, `name`, `role_id`, `is_active`) VALUES
(1, 'superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Administrator', 1, 1);
-- Login: superadmin / password  — Change via Admin Users page after first login.

-- --------------------------------------------------------
-- role_permissions (default matrix)
-- Sections: dashboard, members, events, rsvp, news, gallery, messages, applications, donate, settings, content, role_permissions, admin_users
-- --------------------------------------------------------

-- Super Admin (role_id=1) — full access to everything
INSERT INTO `role_permissions` (`role_id`, `section_key`, `can_view`, `can_edit`, `can_delete`) VALUES
(1,'dashboard',1,1,1),(1,'members',1,1,1),(1,'events',1,1,1),(1,'rsvp',1,1,1),
(1,'news',1,1,1),(1,'gallery',1,1,1),(1,'messages',1,1,1),(1,'applications',1,1,1),
(1,'donate',1,1,1),(1,'settings',1,1,1),(1,'content',1,1,1),(1,'role_permissions',1,1,1),(1,'admin_users',1,1,1);

-- President (role_id=2)
INSERT INTO `role_permissions` (`role_id`, `section_key`, `can_view`, `can_edit`, `can_delete`) VALUES
(2,'dashboard',1,0,0),(2,'members',1,0,0),(2,'events',1,0,0),(2,'rsvp',1,0,0),
(2,'news',1,0,0),(2,'gallery',1,0,0),(2,'messages',1,0,0),(2,'applications',1,1,0),
(2,'donate',0,0,0),(2,'settings',0,0,0),(2,'content',0,0,0),(2,'role_permissions',0,0,0),(2,'admin_users',0,0,0);

-- Vice President (role_id=3)
INSERT INTO `role_permissions` (`role_id`, `section_key`, `can_view`, `can_edit`, `can_delete`) VALUES
(3,'dashboard',1,0,0),(3,'members',1,0,0),(3,'events',1,0,0),(3,'rsvp',1,0,0),
(3,'news',1,0,0),(3,'gallery',1,0,0),(3,'messages',1,0,0),(3,'applications',1,1,0),
(3,'donate',0,0,0),(3,'settings',0,0,0),(3,'content',0,0,0),(3,'role_permissions',0,0,0),(3,'admin_users',0,0,0);

-- Treasurer (role_id=4)
INSERT INTO `role_permissions` (`role_id`, `section_key`, `can_view`, `can_edit`, `can_delete`) VALUES
(4,'dashboard',1,0,0),(4,'members',1,0,0),(4,'events',1,0,0),(4,'rsvp',1,1,0),
(4,'news',0,0,0),(4,'gallery',0,0,0),(4,'messages',0,0,0),(4,'applications',0,0,0),
(4,'donate',1,1,0),(4,'settings',0,0,0),(4,'content',0,0,0),(4,'role_permissions',0,0,0),(4,'admin_users',0,0,0);

-- Secretary (role_id=5)
INSERT INTO `role_permissions` (`role_id`, `section_key`, `can_view`, `can_edit`, `can_delete`) VALUES
(5,'dashboard',1,0,0),(5,'members',1,1,1),(5,'events',1,0,0),(5,'rsvp',0,0,0),
(5,'news',0,0,0),(5,'gallery',0,0,0),(5,'messages',1,1,0),(5,'applications',1,1,0),
(5,'donate',0,0,0),(5,'settings',0,0,0),(5,'content',0,0,0),(5,'role_permissions',0,0,0),(5,'admin_users',0,0,0);

-- Assistant Secretary (role_id=6)
INSERT INTO `role_permissions` (`role_id`, `section_key`, `can_view`, `can_edit`, `can_delete`) VALUES
(6,'dashboard',1,0,0),(6,'members',1,0,0),(6,'events',0,0,0),(6,'rsvp',0,0,0),
(6,'news',0,0,0),(6,'gallery',0,0,0),(6,'messages',1,0,0),(6,'applications',1,0,0),
(6,'donate',0,0,0),(6,'settings',0,0,0),(6,'content',0,0,0),(6,'role_permissions',0,0,0),(6,'admin_users',0,0,0);

-- IT Head (role_id=7)
INSERT INTO `role_permissions` (`role_id`, `section_key`, `can_view`, `can_edit`, `can_delete`) VALUES
(7,'dashboard',1,0,0),(7,'members',1,0,0),(7,'events',1,0,0),(7,'rsvp',0,0,0),
(7,'news',0,0,0),(7,'gallery',1,1,1),(7,'messages',0,0,0),(7,'applications',0,0,0),
(7,'donate',0,0,0),(7,'settings',1,1,0),(7,'content',1,1,0),(7,'role_permissions',0,0,0),(7,'admin_users',0,0,0);

-- Event Manager (role_id=8)
INSERT INTO `role_permissions` (`role_id`, `section_key`, `can_view`, `can_edit`, `can_delete`) VALUES
(8,'dashboard',1,0,0),(8,'members',1,0,0),(8,'events',1,1,1),(8,'rsvp',1,1,0),
(8,'news',1,0,0),(8,'gallery',1,1,0),(8,'messages',0,0,0),(8,'applications',0,0,0),
(8,'donate',0,0,0),(8,'settings',0,0,0),(8,'content',0,0,0),(8,'role_permissions',0,0,0),(8,'admin_users',0,0,0);

-- Social Media Manager (role_id=9)
INSERT INTO `role_permissions` (`role_id`, `section_key`, `can_view`, `can_edit`, `can_delete`) VALUES
(9,'dashboard',1,0,0),(9,'members',1,0,0),(9,'events',1,0,0),(9,'rsvp',0,0,0),
(9,'news',1,1,1),(9,'gallery',1,1,1),(9,'messages',0,0,0),(9,'applications',0,0,0),
(9,'donate',0,0,0),(9,'settings',1,1,0),(9,'content',1,1,0),(9,'role_permissions',0,0,0),(9,'admin_users',0,0,0);

-- --------------------------------------------------------
-- site_settings (all 25+ keys with meaningful defaults)
-- --------------------------------------------------------
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('logo_path', ''),
('favicon_path', ''),
('site_name_en', 'Sarak Youth Development Council'),
('site_name_hi', 'सारक युवा विकास परिषद'),
('site_name_bn', 'সারক যুব উন্নয়ন পরিষদ'),
('tagline_en', 'Empowering Sarak Youth · Building Our Community'),
('tagline_hi', 'सारक युवाओं को सशक्त बनाना · हमारे समुदाय का निर्माण'),
('tagline_bn', 'সারক যুবদের ক্ষমতায়ন · আমাদের সম্প্রদায় গড়ে তোলা'),
('main_motto_en', 'Reconnect. Revive. Rise Together.'),
('main_motto_hi', 'पुनः जुड़ें। पुनः जीवित करें। एक साथ उठें।'),
('main_motto_bn', 'পুনরায় সংযুক্ত হন। পুনরুজ্জীবিত করুন। একসাথে উঠুন।'),
('founding_year', '2024'),
('alert_banner_active', '0'),
('alert_banner_text_en', ''),
('alert_banner_text_hi', ''),
('alert_banner_text_bn', ''),
('notification_email', 'admin@sarakyouth.org'),
('contact_address_en', 'Gunayatan Office, Kolkata, West Bengal, India'),
('contact_address_hi', 'गुणायतन कार्यालय, कोलकाता, पश्चिम बंगाल, भारत'),
('contact_address_bn', 'গুণায়তন অফিস, কলকাতা, পশ্চিমবঙ্গ, ভারত'),
('contact_email', 'contact@sarakyouth.org'),
('contact_phone', '+91 00000 00000'),
('whatsapp_link', ''),
('facebook_url', ''),
('instagram_url', ''),
('youtube_url', ''),
('twitter_url', ''),
('maps_embed', ''),
('donate_bank_name', 'State Bank of India'),
('donate_account_no', ''),
('donate_ifsc', ''),
('donate_upi', ''),
('donate_qr_image', ''),
('stats_members', '50'),
('stats_events', '12'),
('stats_beneficiaries', '500'),
('about_gunayatan_en', 'Gunayatan is a dedicated Jain organization working tirelessly to reconnect the Sarak community with its Jain roots. Through education, social upliftment, and cultural programs, Gunayatan has been a guiding light for thousands of families.'),
('about_gunayatan_hi', 'गुणायतन एक समर्पित जैन संगठन है जो सारक समुदाय को उनकी जैन जड़ों से पुनः जोड़ने के लिए अथक परिश्रम कर रहा है। शिक्षा, सामाजिक उत्थान और सांस्कृतिक कार्यक्रमों के माध्यम से गुणायतन हजारों परिवारों के लिए एक मार्गदर्शक प्रकाश रहा है।'),
('about_gunayatan_bn', 'গুণায়তন একটি নিবেদিত জৈন সংগঠন যা সারক সম্প্রদায়কে তাদের জৈন শিকড়ের সাথে পুনরায় সংযুক্ত করতে অক্লান্তভাবে কাজ করছে। শিক্ষা, সামাজিক উন্নয়ন ও সাংস্কৃতিক অনুষ্ঠানের মাধ্যমে গুণায়তন হাজার হাজার পরিবারের জন্য একটি পথপ্রদর্শক আলো হয়ে উঠেছে।'),
('about_sarak_history_en', 'The Sarak community has ancient Jain origins. Centuries ago, due to social and political pressures, many Sarak families transitioned to Hinduism, losing touch with their original faith and practices. Today, with organizations like Gunayatan leading the way, the Sarak community is experiencing a beautiful renaissance — rediscovering their heritage, traditions, and Jain values.'),
('about_sarak_history_hi', 'सारक समुदाय की प्राचीन जैन उत्पत्ति है। सदियों पहले, सामाजिक और राजनीतिक दबावों के कारण, कई सारक परिवार हिंदू धर्म में चले गए और अपनी मूल आस्था और परंपराओं से दूर हो गए। आज, गुणायतन जैसे संगठनों के नेतृत्व में, सारक समुदाय एक सुंदर पुनर्जागरण का अनुभव कर रहा है।'),
('about_sarak_history_bn', 'সারক সম্প্রদায়ের প্রাচীন জৈন উৎস রয়েছে। শতাব্দী আগে, সামাজিক ও রাজনৈতিক চাপের কারণে, অনেক সারক পরিবার হিন্দু ধর্মে রূপান্তরিত হয়েছিল এবং তাদের মূল বিশ্বাস ও অনুশীলন থেকে বিচ্ছিন্ন হয়ে পড়েছিল।'),
('vision_en', 'To become the leading youth-driven force that empowers every Sarak youth with education, livelihood, spiritual reconnection, and community pride.'),
('vision_hi', 'हर सारक युवा को शिक्षा, आजीविका, आध्यात्मिक पुनः संयोजन और सामुदायिक गौरव के साथ सशक्त बनाने वाला प्रमुख युवा-संचालित बल बनना।'),
('vision_bn', 'প্রতিটি সারক যুবকে শিক্ষা, জীবিকা, আধ্যাত্মিক পুনঃসংযোগ এবং সম্প্রদায়ের গর্বের সাথে ক্ষমতায়িত করার প্রধান যুব-চালিত শক্তি হয়ে ওঠা।'),
('mission_en', 'To organize regular events, skill-building programs, religious siviars, sports activities, and business mentorship that strengthen the Sarak community fabric under the guidance of Gunayatan.'),
('mission_hi', 'गुणायतन के मार्गदर्शन में नियमित कार्यक्रम, कौशल-निर्माण कार्यक्रम, धार्मिक शिविर, खेल गतिविधियां और व्यापार परामर्श आयोजित करना जो सारक समुदाय के ताने-बाने को मजबूत करें।'),
('mission_bn', 'গুণায়তনের নির্দেশনায় নিয়মিত অনুষ্ঠান, দক্ষতা-নির্মাণ কার্যক্রম, ধর্মীয় শিবির, ক্রীড়া কার্যক্রম এবং ব্যবসায়িক পরামর্শ আয়োজন করা।');

-- --------------------------------------------------------
-- members (8 executive + 5 core + 3 advisory = 16 total)
-- Columns: name_en, name_hi, name_bn, designation_en, designation_hi, designation_bn,
--          bio_en, bio_hi, bio_bn, achievements_en, achievements_hi, achievements_bn,
--          photo, category, display_order, email, phone, whatsapp, facebook_url, instagram_url, linkedin_url, is_active
-- --------------------------------------------------------
INSERT INTO `members`
  (`name_en`,`name_hi`,`name_bn`,`designation_en`,`designation_hi`,`designation_bn`,
   `bio_en`,`bio_hi`,`bio_bn`,`achievements_en`,`achievements_hi`,`achievements_bn`,
   `photo`,`category`,`display_order`,`email`,`phone`,`whatsapp`,`facebook_url`,`instagram_url`,`linkedin_url`,`is_active`)
VALUES
(
  'Rajesh Kumar Sarak','राजेश कुमार सारक','রাজেশ কুমার সারক',
  'President','अध्यक्ष','সভাপতি',
  'Rajesh Kumar Sarak is a dynamic community leader with over 15 years of experience in social work and youth development. He has been a driving force behind the formation of the Sarak Youth Development Council.',
  'राजेश कुमार सारक 15 वर्षों से अधिक के सामाजिक कार्य और युवा विकास के अनुभव वाले एक गतिशील सामुदायिक नेता हैं।',
  'রাজেশ কুমার সারক সমাজকর্ম ও যুব উন্নয়নে ১৫ বছরেরও বেশি অভিজ্ঞতাসম্পন্ন একজন গতিশীল সমাজনেতা।',
  'Founded Sarak Youth Development Council; Organized 10+ community events; Youth Leadership Award 2023',
  'सारक युवा विकास परिषद की स्थापना; 10+ सामुदायिक कार्यक्रम आयोजित; युवा नेतृत्व पुरस्कार 2023',
  'সারক যুব উন্নয়ন পরিষদ প্রতিষ্ঠা; ১০+ সম্প্রদায় অনুষ্ঠান আয়োজন; যুব নেতৃত্ব পুরস্কার ২০২৩',
  NULL,'executive',1,'president@sarakyouth.org','+91 98765 43210','+91 98765 43210',NULL,NULL,NULL,1
),
(
  'Priya Devi Sarak','प्रिया देवी सारक','প্রিয়া দেবী সারক',
  'Vice President','उपाध्यक्ष','সহ-সভাপতি',
  'Priya Devi Sarak is a passionate advocate for women''s empowerment and education within the Sarak community. She holds a Master''s degree in Social Work.',
  'प्रिया देवी सारक सारक समुदाय में महिला सशक्तीकरण और शिक्षा की एक भावुक समर्थक हैं। उनके पास सामाजिक कार्य में मास्टर डिग्री है।',
  'প্রিয়া দেবী সারক সারক সম্প্রদায়ের মধ্যে নারী ক্ষমতায়ন ও শিক্ষার একজন উৎসাহী সমর্থক। তাঁর সামাজিক কার্যে স্নাতকোত্তর ডিগ্রি রয়েছে।',
  'MSW Gold Medalist; Women Empowerment Workshop Organizer; Best Volunteer Award 2022',
  'MSW गोल्ड मेडलिस्ट; महिला सशक्तीकरण कार्यशाला आयोजक; सर्वश्रेष्ठ स्वयंसेवक पुरस्कार 2022',
  'MSW গোল্ড মেডেলিস্ট; নারী ক্ষমতায়ন কর্মশালা আয়োজক; সেরা স্বেচ্ছাসেবক পুরস্কার ২০২২',
  NULL,'executive',2,'vp@sarakyouth.org','+91 98765 43211','+91 98765 43211',NULL,NULL,NULL,1
),
(
  'Suresh Jain Sarak','सुरेश जैन सारक','সুরেশ জৈন সারক',
  'Treasurer','कोषाध्यक्ष','কোষাধ্যক্ষ',
  'Suresh Jain Sarak is a chartered accountant and financial advisor who ensures transparent fund management for all council activities.',
  'सुरेश जैन सारक एक चार्टर्ड अकाउंटेंट और वित्तीय सलाहकार हैं जो परिषद की सभी गतिविधियों के लिए पारदर्शी निधि प्रबंधन सुनिश्चित करते हैं।',
  'সুরেশ জৈন সারক একজন চার্টার্ড অ্যাকাউন্ট্যান্ট ও আর্থিক উপদেষ্টা যিনি পরিষদের সমস্ত কার্যক্রমের জন্য স্বচ্ছ তহবিল ব্যবস্থাপনা নিশ্চিত করেন।',
  'CA with 12 years experience; Manages annual budget of 5+ Lakh; Transparency in Finance Award',
  'CA 12 वर्षों के अनुभव के साथ; 5+ लाख के वार्षिक बजट का प्रबंधन; वित्त में पारदर्शिता पुरस्कार',
  '১২ বছরের অভিজ্ঞতাসম্পন্ন CA; ৫+ লক্ষ বার্ষিক বাজেট পরিচালনা; অর্থে স্বচ্ছতা পুরস্কার',
  NULL,'executive',3,'treasurer@sarakyouth.org','+91 98765 43212',NULL,NULL,NULL,NULL,1
),
(
  'Anita Kumari Sarak','अनिता कुमारी सारक','অনিতা কুমারী সারক',
  'Secretary','सचिव','সম্পাদক',
  'Anita Kumari Sarak is responsible for all administrative functions, correspondence, and member management. She is a postgraduate in Business Administration.',
  'अनिता कुमारी सारक सभी प्रशासनिक कार्यों, पत्राचार और सदस्य प्रबंधन के लिए जिम्मेदार हैं। वह व्यवसाय प्रशासन में स्नातकोत्तर हैं।',
  'অনিতা কুমারী সারক সমস্ত প্রশাসনিক কার্যাবলী, চিঠিপত্র এবং সদস্য ব্যবস্থাপনার দায়িত্বে রয়েছেন। তিনি ব্যবসায় প্রশাসনে স্নাতকোত্তর।',
  'MBA; Managed 50+ member records; Streamlined application process; Community Service Medal',
  'MBA; 50+ सदस्य रिकॉर्ड प्रबंधित; आवेदन प्रक्रिया सुव्यवस्थित; सामुदायिक सेवा पदक',
  'MBA; ৫০+ সদস্য রেকর্ড পরিচালনা; আবেদন প্রক্রিয়া সুগঠিত; সম্প্রদায় সেবা পদক',
  NULL,'executive',4,'secretary@sarakyouth.org','+91 98765 43213','+91 98765 43213',NULL,NULL,NULL,1
),
(
  'Mohan Lal Sarak','मोहन लाल सारक','মোহন লাল সারক',
  'Assistant Secretary','सहायक सचिव','সহকারী সম্পাদক',
  'Mohan Lal Sarak assists the Secretary in day-to-day operations and serves as the primary point of contact for new membership inquiries.',
  'मोहन लाल सारक दिन-प्रतिदिन के संचालन में सचिव की सहायता करते हैं और नई सदस्यता पूछताछ के लिए प्राथमिक संपर्क बिंदु के रूप में कार्य करते हैं।',
  'মোহন লাল সারক দৈনন্দিন কার্যক্রমে সম্পাদককে সহায়তা করেন এবং নতুন সদস্যপদ অনুসন্ধানের প্রাথমিক যোগাযোগ বিন্দু হিসেবে কাজ করেন।',
  'Diploma in Office Management; Handled 100+ membership queries; Active volunteer since 2022',
  'कार्यालय प्रबंधन में डिप्लोमा; 100+ सदस्यता प्रश्नों का प्रबंधन; 2022 से सक्रिय स्वयंसेवक',
  'অফিস ম্যানেজমেন্টে ডিপ্লোমা; ১০০+ সদস্যপদ অনুসন্ধান পরিচালনা; ২০২২ থেকে সক্রিয় স্বেচ্ছাসেবক',
  NULL,'executive',5,NULL,'+91 98765 43214',NULL,NULL,NULL,NULL,1
),
(
  'Vikram Singh Sarak','विक्रम सिंह सारक','বিক্রম সিং সারক',
  'IT Head','आईटी प्रमुख','আইটি প্রধান',
  'Vikram Singh Sarak manages the digital presence of the council including the website, social media technical setup, and digital records.',
  'विक्रम सिंह सारक परिषद की डिजिटल उपस्थिति का प्रबंधन करते हैं जिसमें वेबसाइट, सोशल मीडिया तकनीकी सेटअप और डिजिटल रिकॉर्ड शामिल हैं।',
  'বিক্রম সিং সারক পরিষদের ডিজিটাল উপস্থিতি পরিচালনা করেন যার মধ্যে ওয়েবসাইট, সোশ্যাল মিডিয়া প্রযুক্তিগত সেটআপ এবং ডিজিটাল রেকর্ড অন্তর্ভুক্ত।',
  'B.Tech Computer Science; Built council website; Manages digital infrastructure; Tech Volunteer of Year 2023',
  'B.Tech कंप्यूटर विज्ञान; परिषद वेबसाइट निर्मित; डिजिटल इंफ्रास्ट्रक्चर प्रबंधन; 2023 के तकनीकी स्वयंसेवक',
  'B.Tech কম্পিউটার সায়েন্স; পরিষদ ওয়েবসাইট তৈরি; ডিজিটাল অবকাঠামো পরিচালনা; ২০২৩ সালের টেক স্বেচ্ছাসেবক',
  NULL,'executive',6,'it@sarakyouth.org','+91 98765 43215','+91 98765 43215',NULL,NULL,NULL,1
),
(
  'Rahul Prakash Sarak','राहुल प्रकाश सारक','রাহুল প্রকাশ সারক',
  'Event Manager','इवेंट मैनेजर','ইভেন্ট ম্যানেজার',
  'Rahul Prakash Sarak is an enthusiastic event planner who has successfully organized multiple youth gatherings, sports tournaments, and religious siviars.',
  'राहुल प्रकाश सारक एक उत्साही इवेंट प्लानर हैं जिन्होंने कई युवा सभाओं, खेल टूर्नामेंट और धार्मिक शिविरों का सफलतापूर्वक आयोजन किया है।',
  'রাহুল প্রকাশ সারক একজন উৎসাহী ইভেন্ট পরিকল্পনাকারী যিনি সফলভাবে একাধিক যুব সমাবেশ, ক্রীড়া টুর্নামেন্ট এবং ধর্মীয় শিবির আয়োজন করেছেন।',
  'Organized 8 major events; Sports Tournament Director 2023; Best Event Award',
  '8 प्रमुख कार्यक्रम आयोजित; खेल टूर्नामेंट निदेशक 2023; सर्वश्रेष्ठ इवेंट पुरस्कार',
  '৮টি প্রধান অনুষ্ঠান আয়োজন; ক্রীড়া টুর্নামেন্ট পরিচালক ২০২৩; সেরা ইভেন্ট পুরস্কার',
  NULL,'executive',7,NULL,'+91 98765 43216','+91 98765 43216',NULL,NULL,NULL,1
),
(
  'Kavita Sharma Sarak','कविता शर्मा सारक','কবিতা শর্মা সারক',
  'Social Media Manager','सोशल मीडिया मैनेजर','সোশ্যাল মিডিয়া ম্যানেজার',
  'Kavita Sharma Sarak handles all social media channels, content creation, and online outreach for the Sarak Youth Development Council.',
  'कविता शर्मा सारक सारक युवा विकास परिषद के लिए सभी सोशल मीडिया चैनलों, कंटेंट निर्माण और ऑनलाइन आउटरीच का प्रबंधन करती हैं।',
  'কবিতা শর্মা সারক সারক যুব উন্নয়ন পরিষদের সমস্ত সোশ্যাল মিডিয়া চ্যানেল, কন্টেন্ট তৈরি এবং অনলাইন আউটরিচ পরিচালনা করেন।',
  'Grew council social following to 5000+; Content Creator; Digital Marketing Certificate',
  'परिषद की सोशल फॉलोइंग को 5000+ तक बढ़ाया; कंटेंट क्रिएटर; डिजिटल मार्केटिंग सर्टिफिकेट',
  'পরিষদের সোশ্যাল ফলোয়িং ৫০০০+ পর্যন্ত বৃদ্ধি; কন্টেন্ট ক্রিয়েটর; ডিজিটাল মার্কেটিং সার্টিফিকেট',
  NULL,'executive',8,'social@sarakyouth.org','+91 98765 43217','+91 98765 43217',NULL,NULL,NULL,1
),
-- Core Members
(
  'Amit Sarak','अमित सारक','অমিত সারক',
  'Core Member — Education','मुख्य सदस्य — शिक्षा','মূল সদস্য — শিক্ষা',
  'Amit Sarak focuses on education initiatives and scholarship programs for Sarak community youth.',
  'अमित सारक सारक समुदाय के युवाओं के लिए शिक्षा पहल और छात्रवृत्ति कार्यक्रमों पर ध्यान केंद्रित करते हैं।',
  'অমিত সারক সারক সম্প্রদায়ের যুবদের জন্য শিক্ষা উদ্যোগ এবং বৃত্তি কার্যক্রমে মনোযোগ দেন।',
  'Facilitated 20+ scholarships; Education Mentor; B.Ed Degree',
  '20+ छात्रवृत्ति सुगम बनाई; शिक्षा मेंटर; B.Ed डिग्री',
  '২০+ বৃত্তি সহজতর করেছেন; শিক্ষা মেন্টর; B.Ed ডিগ্রি',
  NULL,'core',1,NULL,'+91 98765 43220',NULL,NULL,NULL,NULL,1
),
(
  'Sunita Devi Sarak','सुनिता देवी सारक','সুনিতা দেবী সারক',
  'Core Member — Women''s Wing','मुख्य सदस्य — महिला विंग','মূল সদস্য — মহিলা উইং',
  'Sunita Devi Sarak leads women-centric programs, health camps, and skill development workshops.',
  'सुनिता देवी सारक महिला-केंद्रित कार्यक्रमों, स्वास्थ्य शिविरों और कौशल विकास कार्यशालाओं का नेतृत्व करती हैं।',
  'সুনিতা দেবী সারক নারী-কেন্দ্রিক কার্যক্রম, স্বাস্থ্য শিবির এবং দক্ষতা উন্নয়ন কর্মশালার নেতৃত্ব দেন।',
  'Organized 5 health camps; Women skill dev workshops; Community health volunteer',
  '5 स्वास्थ्य शिविर आयोजित; महिला कौशल विकास कार्यशालाएं; सामुदायिक स्वास्थ्य स्वयंसेवक',
  '৫টি স্বাস্থ্য শিবির আয়োজন; নারী দক্ষতা উন্নয়ন কর্মশালা; সম্প্রদায় স্বাস্থ্য স্বেচ্ছাসেবক',
  NULL,'core',2,NULL,'+91 98765 43221',NULL,NULL,NULL,NULL,1
),
(
  'Deepak Sarak','दीपक सारक','দীপক সারক',
  'Core Member — Sports','मुख्य सदस्य — खेल','মূল সদস্য — ক্রীড়া',
  'Deepak Sarak coordinates all sports activities and tournaments for Sarak youth.',
  'दीपक सारक सारक युवाओं के लिए सभी खेल गतिविधियों और टूर्नामेंट का समन्वय करते हैं।',
  'দীপক সারক সারক যুবদের জন্য সমস্ত ক্রীড়া কার্যক্রম ও টুর্নামেন্ট সমন্বয় করেন।',
  'State level kabaddi player; Organized inter-community cricket tournament; Sports Coordinator',
  'राज्य स्तरीय कबड्डी खिलाड़ी; अंतर-समुदाय क्रिकेट टूर्नामेंट आयोजित; खेल समन्वयक',
  'রাজ্য স্তরের কাবাডি খেলোয়াড়; আন্তঃসম্প্রদায় ক্রিকেট টুর্নামেন্ট আয়োজন; ক্রীড়া সমন্বয়কারী',
  NULL,'core',3,NULL,'+91 98765 43222',NULL,NULL,NULL,NULL,1
),
(
  'Riya Jain Sarak','रिया जैन सारक','রিয়া জৈন সারক',
  'Core Member — Cultural Affairs','मुख्य सदस्य — सांस्कृतिक कार्य','মূল সদস্য — সাংস্কৃতিক বিষয়',
  'Riya Jain Sarak manages cultural events, religious programs, and preservation of Sarak heritage.',
  'रिया जैन सारक सांस्कृतिक कार्यक्रमों, धार्मिक कार्यक्रमों और सारक विरासत के संरक्षण का प्रबंधन करती हैं।',
  'রিয়া জৈন সারক সাংস্কৃতিক অনুষ্ঠান, ধর্মীয় কার্যক্রম এবং সারক ঐতিহ্য সংরক্ষণ পরিচালনা করেন।',
  'Organized Paryushan celebrations; Cultural Ambassador; Classical dance performer',
  'पर्युषण समारोह आयोजित; सांस्कृतिक राजदूत; शास्त्रीय नृत्य कलाकार',
  'পর্যুষণ উৎসব আয়োজন; সাংস্কৃতিক রাষ্ট্রদূত; শাস্ত্রীয় নৃত্য শিল্পী',
  NULL,'core',4,NULL,'+91 98765 43223',NULL,NULL,NULL,NULL,1
),
(
  'Santosh Kumar Sarak','संतोष कुमार सारक','সন্তোষ কুমার সারক',
  'Core Member — Business & Livelihood','मुख्य सदस्य — व्यापार एवं आजीविका','মূল সদস্য — ব্যবসা ও জীবিকা',
  'Santosh Kumar Sarak mentors young Sarak entrepreneurs and connects members with business opportunities.',
  'संतोष कुमार सारक युवा सारक उद्यमियों को मार्गदर्शन देते हैं और सदस्यों को व्यापार के अवसरों से जोड़ते हैं।',
  'সন্তোষ কুমার সারক তরুণ সারক উদ্যোক্তাদের পরামর্শ দেন এবং সদস্যদের ব্যবসায়িক সুযোগের সাথে সংযুক্ত করেন।',
  'Mentored 15+ entrepreneurs; Business network of 50+ Sarak businesses; Startup Guru Award',
  '15+ उद्यमियों को मेंटर किया; 50+ सारक व्यवसायों का बिजनेस नेटवर्क; स्टार्टअप गुरु पुरस्कार',
  '১৫+ উদ্যোক্তাকে পরামর্শ দেওয়া; ৫০+ সারক ব্যবসার ব্যবসায়িক নেটওয়ার্ক; স্টার্টআপ গুরু পুরস্কার',
  NULL,'core',5,NULL,'+91 98765 43224',NULL,NULL,NULL,NULL,1
),
-- Advisory Members
(
  'Dr. Ramesh Chandra Jain','डॉ. रमेश चंद्र जैन','ড. রমেশ চন্দ্র জৈন',
  'Advisory Member — Spiritual Guide','सलाहकार सदस्य — आध्यात्मिक मार्गदर्शक','উপদেষ্টা সদস্য — আধ্যাত্মিক গাইড',
  'Dr. Ramesh Chandra Jain is a renowned Jain scholar and spiritual guide who provides religious direction to the council.',
  'डॉ. रमेश चंद्र जैन एक प्रसिद्ध जैन विद्वान और आध्यात्मिक मार्गदर्शक हैं जो परिषद को धार्मिक दिशा प्रदान करते हैं।',
  'ড. রমেশ চন্দ্র জৈন একজন বিখ্যাত জৈন পণ্ডিত ও আধ্যাত্মিক গাইড যিনি পরিষদকে ধর্মীয় দিকনির্দেশনা প্রদান করেন।',
  'PhD in Jain Philosophy; Author of 5 books on Jainism; Padma Shri nominee',
  'जैन दर्शन में पीएचडी; जैन धर्म पर 5 पुस्तकों के लेखक; पद्म श्री नामित',
  'জৈন দর্শনে পিএইচডি; জৈন ধর্মের উপর ৫টি বইয়ের লেখক; পদ্মশ্রী মনোনীত',
  NULL,'advisory',1,NULL,'+91 98765 43230',NULL,NULL,NULL,NULL,1
),
(
  'Smt. Kamla Devi Sarak','श्रीमती कमला देवी सारक','শ্রীমতী কমলা দেবী সারক',
  'Advisory Member — Elder Representative','सलाहकार सदस्य — वरिष्ठ प्रतिनिधि','উপদেষ্টা সদস্য — প্রবীণ প্রতিনিধি',
  'Smt. Kamla Devi Sarak is a respected elder of the Sarak community who provides wisdom and cultural guidance to the youth council.',
  'श्रीमती कमला देवी सारक सारक समुदाय की एक सम्मानित बुजुर्ग हैं जो युवा परिषद को ज्ञान और सांस्कृतिक मार्गदर्शन प्रदान करती हैं।',
  'শ্রীমতী কমলা দেবী সারক সারক সম্প্রদায়ের একজন সম্মানিত প্রবীণ যিনি যুব পরিষদকে জ্ঞান ও সাংস্কৃতিক নির্দেশনা প্রদান করেন।',
  'Community leader for 30+ years; Oral historian of Sarak traditions; Lokshiksha Ratna Award',
  '30+ वर्षों से सामुदायिक नेता; सारक परंपराओं की मौखिक इतिहासकार; लोकशिक्षा रत्न पुरस्कार',
  '৩০+ বছর ধরে সম্প্রদায় নেতা; সারক ঐতিহ্যের মৌখিক ইতিহাসবিদ; লোকশিক্ষা রত্ন পুরস্কার',
  NULL,'advisory',2,NULL,'+91 98765 43231',NULL,NULL,NULL,NULL,1
),
(
  'Prof. Arun Kumar Sarak','प्रो. अरुण कुमार सारक','প্রফেসর অরুণ কুমার সারক',
  'Advisory Member — Academic Advisor','सलाहकार सदस्य — शैक्षणिक सलाहकार','উপদেষ্টা সদস্য — একাডেমিক উপদেষ্টা',
  'Prof. Arun Kumar Sarak is a retired university professor who guides educational policies and scholarship programs of the council.',
  'प्रो. अरुण कुमार सारक एक सेवानिवृत्त विश्वविद्यालय प्रोफेसर हैं जो परिषद की शैक्षिक नीतियों और छात्रवृत्ति कार्यक्रमों का मार्गदर्शन करते हैं।',
  'প্রফেসর অরুণ কুমার সারক একজন অবসরপ্রাপ্ত বিশ্ববিদ্যালয় অধ্যাপক যিনি পরিষদের শিক্ষা নীতি ও বৃত্তি কার্যক্রমে নির্দেশনা দেন।',
  'PhD in Sociology; 25 years as Professor; Authored research on Sarak community; Best Educator Award',
  'समाजशास्त्र में पीएचडी; 25 वर्ष प्रोफेसर के रूप में; सारक समुदाय पर शोध; सर्वश्रेष्ठ शिक्षक पुरस्कार',
  'সমাজবিজ্ঞানে পিএইচডি; ২৫ বছর অধ্যাপক হিসেবে; সারক সম্প্রদায়ের উপর গবেষণা; সেরা শিক্ষক পুরস্কার',
  NULL,'advisory',3,NULL,'+91 98765 43232',NULL,NULL,NULL,NULL,1
);

-- --------------------------------------------------------
-- events (3 upcoming + 3 past)
-- --------------------------------------------------------
INSERT INTO `events`
  (`title_en`,`title_hi`,`title_bn`,`description_en`,`description_hi`,`description_bn`,
   `event_date`,`event_time`,`location_en`,`location_hi`,`location_bn`,
   `type`,`cover_image`,`status`,`rsvp_enabled`,`max_attendees`)
VALUES
(
  'Annual Jain Paryushan Sivir 2025',
  'वार्षिक जैन पर्युषण शिविर 2025',
  'বার্ষিক জৈন পর্যুষণ শিবির ২০২৫',
  'Join us for a spiritually enriching 8-day Paryushan Sivir organized for Sarak youth. Activities include pravachans, meditation, swadhyay sessions, and samooh puja. Open for all Sarak youth aged 15-35.',
  'सारक युवाओं के लिए आयोजित 8-दिवसीय पर्युषण शिविर में आध्यात्मिक रूप से समृद्ध अनुभव के लिए हमसे जुड़ें। गतिविधियों में प्रवचन, ध्यान, स्वाध्याय सत्र और समूह पूजा शामिल हैं।',
  'সারক যুবদের জন্য আয়োজিত ৮ দিনের পর্যুষণ শিবিরে আধ্যাত্মিকভাবে সমৃদ্ধ অভিজ্ঞতার জন্য আমাদের সাথে যোগ দিন।',
  DATE_ADD(CURDATE(), INTERVAL 30 DAY),'08:00:00',
  'Jain Mandir Hall, Kolkata','जैन मंदिर हॉल, कोलकाता','জৈন মন্দির হল, কলকাতা',
  'religious',NULL,'upcoming',1,200
),
(
  'Sarak Youth Sports Tournament 2025',
  'सारक युवा खेल टूर्नामेंट 2025',
  'সারক যুব ক্রীড়া টুর্নামেন্ট ২০২৫',
  'Annual sports tournament featuring cricket, kabaddi, badminton, and chess competitions. Open for Sarak youth teams. Prizes and certificates for winners.',
  'क्रिकेट, कबड्डी, बैडमिंटन और शतरंज प्रतियोगिताओं वाला वार्षिक खेल टूर्नामेंट। सारक युवा टीमों के लिए खुला। विजेताओं के लिए पुरस्कार और प्रमाण पत्र।',
  'ক্রিকেট, কাবাডি, ব্যাডমিন্টন ও দাবা প্রতিযোগিতা সম্বলিত বার্ষিক ক্রীড়া টুর্নামেন্ট। সারক যুব দলের জন্য উন্মুক্ত। বিজয়ীদের জন্য পুরস্কার ও সার্টিফিকেট।',
  DATE_ADD(CURDATE(), INTERVAL 45 DAY),'07:00:00',
  'District Sports Ground, Kolkata','जिला खेल मैदान, कोलकाता','জেলা ক্রীড়া মাঠ, কলকাতা',
  'sports',NULL,'upcoming',1,300
),
(
  'Career & Business Mentorship Workshop',
  'करियर और व्यापार परामर्श कार्यशाला',
  'ক্যারিয়ার ও ব্যবসায়িক পরামর্শ কর্মশালা',
  'A full-day workshop connecting Sarak youth with experienced professionals and entrepreneurs. Topics: resume building, interview skills, starting a business, digital marketing, and government schemes.',
  'सारक युवाओं को अनुभवी पेशेवरों और उद्यमियों से जोड़ने वाली पूर्णदिवसीय कार्यशाला। विषय: रिज्यूमे निर्माण, साक्षात्कार कौशल, व्यापार शुरू करना, डिजिटल मार्केटिंग।',
  'সারক যুবদের অভিজ্ঞ পেশাদার ও উদ্যোক্তাদের সাথে সংযুক্ত করার একদিনের কর্মশালা।',
  DATE_ADD(CURDATE(), INTERVAL 60 DAY),'10:00:00',
  'Community Hall, Howrah','सामुदायिक हॉल, हावड़ा','কমিউনিটি হল, হাওড়া',
  'business',NULL,'upcoming',1,150
),
(
  'Diwali Cultural Program 2024',
  'दिवाली सांस्कृतिक कार्यक्रम 2024',
  'দীপাবলি সাংস্কৃতিক অনুষ্ঠান ২০২৪',
  'A vibrant cultural evening celebrating Diwali with traditional performances, music, and community dinner. Brought together 300+ Sarak families.',
  'पारंपरिक प्रदर्शन, संगीत और सामुदायिक भोजन के साथ दिवाली मनाने वाली एक जीवंत सांस्कृतिक संध्या। 300+ सारक परिवारों को एक साथ लाया।',
  'ঐতিহ্যবাহী পরিবেশনা, সংগীত ও সম্প্রদায়িক রাতের খাবার সহ দীপাবলি উদযাপনের একটি প্রাণবন্ত সাংস্কৃতিক সন্ধ্যা।',
  DATE_SUB(CURDATE(), INTERVAL 60 DAY),'06:00:00',
  'Town Hall, Kolkata','टाउन हॉल, कोलकाता','টাউন হল, কলকাতা',
  'religious',NULL,'completed',0,NULL
),
(
  'Education Scholarship Distribution 2024',
  'शिक्षा छात्रवृत्ति वितरण 2024',
  'শিক্ষা বৃত্তি বিতরণ ২০২৪',
  'Annual scholarship distribution ceremony where 25 deserving Sarak students were awarded scholarships for higher education, fully funded by Gunayatan.',
  'वार्षिक छात्रवृत्ति वितरण समारोह जहां गुणायतन द्वारा पूर्णतः वित्त पोषित, उच्च शिक्षा के लिए 25 योग्य सारक छात्रों को छात्रवृत्ति प्रदान की गई।',
  'বার্ষিক বৃত্তি বিতরণ অনুষ্ঠানে গুণায়তন কর্তৃক সম্পূর্ণ অর্থায়িত উচ্চ শিক্ষার জন্য ২৫ জন মেধাবী সারক শিক্ষার্থীকে বৃত্তি প্রদান করা হয়।',
  DATE_SUB(CURDATE(), INTERVAL 90 DAY),'11:00:00',
  'Gunayatan Office, Kolkata','गुणायतन कार्यालय, कोलकाता','গুণায়তন অফিস, কলকাতা',
  'education',NULL,'completed',0,NULL
),
(
  'Sarak Community Health Camp',
  'सारक सामुदायिक स्वास्थ्य शिविर',
  'সারক সম্প্রদায় স্বাস্থ্য শিবির',
  'Free health check-up camp for Sarak community members. Services included blood tests, eye check-up, dental check-up, and doctor consultations. 150+ community members benefited.',
  'सारक समुदाय के सदस्यों के लिए निःशुल्क स्वास्थ्य जांच शिविर। सेवाओं में रक्त परीक्षण, नेत्र जांच, दंत जांच और डॉक्टर परामर्श शामिल थे। 150+ समुदाय सदस्यों को लाभ हुआ।',
  'সারক সম্প্রদায়ের সদস্যদের জন্য বিনামূল্যে স্বাস্থ্য পরীক্ষা শিবির। পরিষেবায় ছিল রক্ত পরীক্ষা, চক্ষু পরীক্ষা, দন্ত পরীক্ষা ও ডাক্তার পরামর্শ। ১৫০+ সম্প্রদায় সদস্য উপকৃত হয়েছেন।',
  DATE_SUB(CURDATE(), INTERVAL 120 DAY),'09:00:00',
  'Primary Health Centre, Howrah','प्राथमिक स्वास्थ्य केंद्र, हावड़ा','প্রাথমিক স্বাস্থ্য কেন্দ্র, হাওড়া',
  'general',NULL,'completed',0,NULL
);

-- --------------------------------------------------------
-- news (5 articles — 1 flagged as alert)
-- --------------------------------------------------------
INSERT INTO `news`
  (`title_en`,`title_hi`,`title_bn`,`content_en`,`content_hi`,`content_bn`,`category`,`cover_image`,`is_alert`)
VALUES
(
  'Registration Open: Paryushan Sivir 2025',
  'पंजीकरण खुला: पर्युषण शिविर 2025',
  'নিবন্ধন শুরু: পর্যুষণ শিবির ২০২৫',
  'We are thrilled to announce that registrations are now open for the Annual Paryushan Sivir 2025. This 8-day spiritual retreat is open for all Sarak youth aged 15-35. Seats are limited to 200 participants. Register early to secure your spot. The sivir will include pravachans by renowned Jain scholars, group meditation sessions, swadhyay, and samooh puja. Accommodation and meals will be provided. For more details, contact the Secretary.',
  'हमें यह घोषणा करते हुए प्रसन्नता हो रही है कि वार्षिक पर्युषण शिविर 2025 के लिए पंजीकरण अब खुल गया है। यह 8-दिवसीय आध्यात्मिक एकांतवास 15-35 वर्ष की आयु के सभी सारक युवाओं के लिए खुला है। सीटें 200 प्रतिभागियों तक सीमित हैं।',
  'আমরা আনন্দের সাথে ঘোষণা করছি যে বার্ষিক পর্যুষণ শিবির ২০২৫-এর জন্য নিবন্ধন এখন শুরু হয়েছে। এই ৮ দিনের আধ্যাত্মিক আশ্রম ১৫-৩৫ বছর বয়সী সমস্ত সারক যুবদের জন্য উন্মুক্ত।',
  'announcement','',1
),
(
  'Sarak Youth Development Council Officially Formed',
  'सारक युवा विकास परिषद का आधिकारिक गठन',
  'সারক যুব উন্নয়ন পরিষদ আনুষ্ঠানিকভাবে গঠিত',
  'Under the gracious guidance of Gunayatan, the Sarak Youth Development Council has been officially formed. The council consists of dedicated young leaders from the Sarak community committed to the upliftment, education, and cultural reconnection of our community. The inaugural ceremony was graced by senior Gunayatan officials and community elders.',
  'गुणायतन के सौम्य मार्गदर्शन में, सारक युवा विकास परिषद का आधिकारिक गठन हो गया है। परिषद में हमारे समुदाय के उत्थान, शिक्षा और सांस्कृतिक पुनः संयोजन के प्रति समर्पित सारक समुदाय के युवा नेता शामिल हैं।',
  'গুণায়তনের সদয় নির্দেশনায় সারক যুব উন্নয়ন পরিষদ আনুষ্ঠানিকভাবে গঠিত হয়েছে। পরিষদে আমাদের সম্প্রদায়ের উন্নয়ন, শিক্ষা ও সাংস্কৃতিক পুনঃসংযোগে নিবেদিত সারক সম্প্রদায়ের তরুণ নেতারা রয়েছেন।',
  'news','',0
),
(
  '25 Sarak Students Receive Scholarships from Gunayatan',
  '25 सारक छात्रों को गुणायतन से छात्रवृत्ति मिली',
  'গুণায়তন থেকে ২৫ জন সারক শিক্ষার্থী বৃত্তি পেলেন',
  'In a heartwarming ceremony held at the Gunayatan office, 25 meritorious students from the Sarak community were awarded scholarships for higher education. The scholarships cover tuition fees, books, and living expenses. This initiative by Gunayatan is a testament to its commitment to the educational upliftment of the Sarak community.',
  'गुणायतन कार्यालय में आयोजित एक भावपूर्ण समारोह में, सारक समुदाय के 25 मेधावी छात्रों को उच्च शिक्षा के लिए छात्रवृत्ति प्रदान की गई। छात्रवृत्ति में ट्यूशन फीस, किताबें और रहने का खर्च शामिल है।',
  'গুণায়তন অফিসে অনুষ্ঠিত একটি হৃদয়গ্রাহী অনুষ্ঠানে সারক সম্প্রদায়ের ২৫ জন মেধাবী শিক্ষার্থীকে উচ্চ শিক্ষার জন্য বৃত্তি প্রদান করা হয়।',
  'news','',0
),
(
  'New Membership Drive: Join the Sarak Youth Council',
  'नई सदस्यता अभियान: सारक युवा परिषद में शामिल हों',
  'নতুন সদস্যপদ অভিযান: সারক যুব পরিষদে যোগ দিন',
  'We are launching a new membership drive to welcome more Sarak youth into our growing council. If you are between 18-35 years old and belong to the Sarak community, we invite you to apply for membership. Members get access to exclusive events, skill workshops, mentorship programs, and our growing network of Sarak professionals.',
  'हम अपनी बढ़ती परिषद में अधिक सारक युवाओं का स्वागत करने के लिए एक नया सदस्यता अभियान शुरू कर रहे हैं। यदि आप 18-35 वर्ष की आयु के हैं और सारक समुदाय से संबंधित हैं, तो हम आपको सदस्यता के लिए आवेदन करने के लिए आमंत्रित करते हैं।',
  'আমরা আমাদের ক্রমবর্ধমান পরিষদে আরও সারক যুবদের স্বাগত জানাতে একটি নতুন সদস্যপদ অভিযান শুরু করছি।',
  'announcement','',0
),
(
  'Sports Tournament: Sarak Youth Wins District Kabaddi Championship',
  'खेल टूर्नामेंट: सारक युवा ने जिला कबड्डी चैंपियनशिप जीती',
  'ক্রীড়া টুর্নামেন্ট: সারক যুব জেলা কাবাডি চ্যাম্পিয়নশিপ জিতেছে',
  'Congratulations to the Sarak Youth Kabaddi team for winning the District Kabaddi Championship! Our team, coached by Core Member Deepak Sarak, defeated 12 other teams to clinch the trophy. This victory brings immense pride to the entire Sarak community. The winning team will be felicitated at the upcoming annual function.',
  'सारक युवा कबड्डी टीम को जिला कबड्डी चैंपियनशिप जीतने पर बधाई! हमारी टीम ने ट्रॉफी जीतने के लिए 12 अन्य टीमों को हराया। यह जीत पूरे सारक समुदाय के लिए अत्यधिक गर्व लेकर आती है।',
  'সারক যুব কাবাডি দলকে জেলা কাবাডি চ্যাম্পিয়নশিপ জেতার জন্য অভিনন্দন! আমাদের দল ট্রফি জিততে ১২টি দলকে পরাজিত করেছে।',
  'achievement','',0
);

-- --------------------------------------------------------
-- gallery (10 photos)
-- --------------------------------------------------------
INSERT INTO `gallery` (`image_path`,`caption_en`,`caption_hi`,`caption_bn`,`event_id`,`year`,`is_video`,`video_url`) VALUES
(NULL,'Paryushan Sivir 2024 — Opening Ceremony','पर्युषण शिविर 2024 — उद्घाटन समारोह','পর্যুষণ শিবির ২০২৪ — উদ্বোধনী অনুষ্ঠান',NULL,YEAR(CURDATE()),0,NULL),
(NULL,'Diwali Cultural Program — Stage Performance','दिवाली सांस्कृतिक कार्यक्रम — मंच प्रदर्शन','দীপাবলি সাংস্কৃতিক অনুষ্ঠান — মঞ্চ পরিবেশনা',4,YEAR(CURDATE()),0,NULL),
(NULL,'Scholarship Distribution Ceremony 2024','छात्रवृत्ति वितरण समारोह 2024','বৃত্তি বিতরণ অনুষ্ঠান ২০২৪',5,YEAR(CURDATE()),0,NULL),
(NULL,'Health Camp — Free Check-up for Community','स्वास्थ्य शिविर — समुदाय के लिए निःशुल्क जांच','স্বাস্থ্য শিবির — সম্প্রদায়ের জন্য বিনামূল্যে পরীক্ষা',6,YEAR(CURDATE()),0,NULL),
(NULL,'Sarak Youth Sports Team — District Champions','सारक युवा खेल टीम — जिला चैंपियन','সারক যুব ক্রীড়া দল — জেলা চ্যাম্পিয়ন',NULL,YEAR(CURDATE()),0,NULL),
(NULL,'Community Meeting — Youth Council Formation','सामुदायिक बैठक — युवा परिषद का गठन','কমিউনিটি মিটিং — যুব পরিষদ গঠন',NULL,YEAR(CURDATE()),0,NULL),
(NULL,'Religious Swadhyay Session','धार्मिक स्वाध्याय सत्र','ধর্মীয় স্বাধ্যায় সেশন',NULL,YEAR(CURDATE()),0,NULL),
(NULL,'Business Workshop — Entrepreneurs Connect','व्यापार कार्यशाला — उद्यमी कनेक्ट','ব্যবসায়িক কর্মশালা — উদ্যোক্তা সংযোগ',NULL,YEAR(CURDATE()),0,NULL),
(NULL,'Women''s Skill Development Workshop','महिला कौशल विकास कार्यशाला','নারী দক্ষতা উন্নয়ন কর্মশালা',NULL,YEAR(CURDATE()),0,NULL),
(NULL,'Sarak Youth Council — Annual Gathering 2024','सारक युवा परिषद — वार्षिक सभा 2024','সারক যুব পরিষদ — বার্ষিক সভা ২০২৪',NULL,YEAR(CURDATE()),0,NULL);

SET FOREIGN_KEY_CHECKS = 1;
