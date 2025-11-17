
  /* ================= giữ nguyên articleDetails từ code gốc (mình giữ nguyên y chang) ================= */
  const articleDetails = {

  // 1️⃣ Khám phá hoạt động nổi bật
  "Khám phá hoạt động nổi bật": {
    date: "20/10/2025",
    author: "Ban biên tập",
    views: 123,
    content: `<p>Trong thế giới hiện đại, việc trẻ em tham gia các hoạt động đa dạng là yếu tố quan trọng trong phát triển toàn diện...</p>
<p>Các hoạt động thể chất như thể dục, vận động ngoài trời giúp trẻ rèn luyện sức khỏe, học cách phối hợp tay chân và tăng cường sức bền...</p>
<p>Các hoạt động trí tuệ như STEM, thí nghiệm khoa học và bài học sáng tạo nuôi dưỡng tinh thần khám phá, khả năng tư duy logic và giải quyết vấn đề...</p>
<p>Hoạt động xã hội giúp trẻ học cách hợp tác, chia sẻ, cảm thông và giao tiếp hiệu quả thông qua dự án nhóm, trò chơi hợp tác và các hoạt động cộng đồng...</p>
<p>Nghệ thuật như vẽ, nhạc, múa giúp trẻ biểu đạt cảm xúc, phát triển sáng tạo và tự tin khi trình bày sản phẩm của mình...</p>
<p>Mỗi hoạt động được thiết kế dựa trên nghiên cứu tâm lý, đảm bảo an toàn, phù hợp độ tuổi, và giáo viên đồng hành hướng dẫn, giám sát...</p>
<p>Kết quả là trẻ khỏe mạnh, tự tin, sáng tạo, biết chia sẻ và quan tâm cộng đồng. Phụ huynh nhận thấy sự thay đổi tích cực về kỹ năng, thái độ và hành vi...</p>
<p>Như vậy, các hoạt động nổi bật là chìa khóa giúp trẻ phát triển toàn diện về thể chất, trí tuệ và cảm xúc, chuẩn bị cho tương lai năng động và hạnh phúc.</p>`
  },

  // 2️⃣ Quyền được học tập
  "Quyền được học tập": {
    date: "18/10/2025",
    author: "Ban biên tập",
    views: 98,
    content: `<p>Mọi trẻ em đều có quyền tiếp cận giáo dục chất lượng, bất kể giới tính, địa vị hay hoàn cảnh gia đình. Quyền này được khẳng định trong Công ước Liên Hiệp Quốc về quyền trẻ em...</p>
<p>Giáo dục hình thành kỹ năng tư duy phản biện, sáng tạo và giải quyết vấn đề. Môi trường học tập tích cực khuyến khích trẻ thử nghiệm, sai sót và học hỏi...</p>
<p>Chương trình học và hoạt động ngoại khóa phát triển kỹ năng mềm, kỹ năng sống, rèn luyện thể chất, trí tuệ, cảm xúc...</p>
<p>Trẻ em dễ bị tổn thương như trẻ khuyết tật, vùng sâu vùng xa cần được hỗ trợ đặc biệt về cơ sở vật chất, giáo trình và phương pháp giảng dạy...</p>
<p>Những trẻ tiếp cận giáo dục chất lượng thường tự tin, độc lập, tôn trọng người khác và dễ hòa nhập xã hội. Quyền học tập là chìa khóa mở ra cơ hội phát triển...</p>
<p>Mỗi gia đình, nhà giáo và cộng đồng cần chung tay bảo vệ và thực hiện quyền học tập một cách toàn diện và bền vững...</p>`
  },

  // 3️⃣ Quyền được chăm sóc sức khỏe
  "Quyền được chăm sóc sức khỏe": {
    date: "17/10/2025",
    author: "Ban biên tập",
    views: 87,
    content: `<p>Trẻ em có quyền được chăm sóc sức khỏe toàn diện, bao gồm dinh dưỡng, y tế, chăm sóc tinh thần và phòng chống bệnh tật. Quyền này giúp trẻ phát triển khỏe mạnh và hạnh phúc...</p>
<p>Dinh dưỡng hợp lý, giấc ngủ đầy đủ, vận động thường xuyên là nền tảng quan trọng cho sự phát triển thể chất...</p>
<p>Chăm sóc y tế định kỳ, tiêm chủng đầy đủ và theo dõi sức khỏe giúp phát hiện sớm bệnh lý và phòng ngừa biến chứng...</p>
<p>Chăm sóc tinh thần bao gồm giáo dục cảm xúc, tư vấn tâm lý, môi trường gia đình và lớp học an toàn, khuyến khích trẻ bày tỏ cảm xúc và giải quyết xung đột...</p>
<p>Trẻ em trong hoàn cảnh khó khăn, vùng sâu vùng xa cần được hỗ trợ về y tế cơ bản, dinh dưỡng và phòng chống dịch bệnh...</p>
<p>Thực hiện quyền chăm sóc sức khỏe giúp trẻ phát triển toàn diện, nâng cao chất lượng cuộc sống và chuẩn bị cho tương lai năng động, hạnh phúc...</p>`
  },

  // 4️⃣ Quyền được bảo vệ
  "Quyền được bảo vệ": {
    date: "16/10/2025",
    author: "Ban biên tập",
    views: 74,
    content: `<p>Mọi trẻ em có quyền được bảo vệ khỏi bạo lực, xâm hại, bóc lột và mọi hình thức phân biệt đối xử. Đây là quyền cơ bản, được pháp luật và cộng đồng quốc tế bảo đảm...</p>
<p>Gia đình, nhà trường và xã hội cần xây dựng môi trường an toàn, tạo điều kiện trẻ phát triển toàn diện về thể chất, trí tuệ và tinh thần...</p>
<p>Các chương trình giáo dục kỹ năng sống, giáo dục giới tính, kỹ năng tự vệ, nhận biết nguy hiểm giúp trẻ tự bảo vệ bản thân...</p>
<p>Trẻ em gặp nguy cơ cao cần sự can thiệp kịp thời, tư vấn tâm lý, hỗ trợ pháp lý và dịch vụ bảo vệ...</p>
<p>Bảo vệ quyền trẻ em là trách nhiệm chung, giúp trẻ tự tin, hạnh phúc, phát triển bền vững và chuẩn bị cho tương lai...</p>`
  },

  // 5️⃣ Dự án học đường
  "Dự án học đường": {
    date: "15/10/2025",
    author: "Ban biên tập",
    views: 65,
    content: `<p>Dự án học đường nhằm xây dựng môi trường học tập thân thiện, sáng tạo và khuyến khích học tập trải nghiệm. Các em tham gia các dự án STEM, câu lạc bộ khoa học, nghệ thuật...</p>
<p>Học tập trải nghiệm giúp trẻ phát triển tư duy logic, kỹ năng giải quyết vấn đề, làm việc nhóm và sáng tạo...</p>
<p>Giáo viên đóng vai trò hướng dẫn, hỗ trợ và tạo cơ hội cho trẻ thử nghiệm, sai sót, rút ra bài học...</p>
<p>Kết hợp dự án học đường với công nghệ, các em học lập trình, robotics, mô hình khoa học, phát triển kỹ năng 4.0...</p>
<p>Mỗi dự án đều nhấn mạnh sự hợp tác, chia sẻ, tinh thần trách nhiệm, giúp trẻ hình thành kỹ năng mềm cần thiết cho tương lai...</p>`
  },

  // 6️⃣ Dự án cộng đồng
  "Dự án cộng đồng": {
    date: "14/10/2025",
    author: "Ban biên tập",
    views: 58,
    content: `<p>Dự án cộng đồng giúp trẻ rèn luyện kỹ năng xã hội, tinh thần hợp tác và trách nhiệm với mọi người xung quanh. Các hoạt động như làm từ thiện, dọn dẹp môi trường, chăm sóc người già...</p>
<p>Tham gia dự án cộng đồng giúp trẻ phát triển lòng nhân ái, kỹ năng giao tiếp, hợp tác, giải quyết xung đột...</p>
<p>Trẻ học cách lên kế hoạch, thực hiện và đánh giá kết quả dự án, rèn luyện kỹ năng lãnh đạo, quản lý thời gian và trách nhiệm...</p>
<p>Đây là cơ hội để trẻ kết nối với cộng đồng, học hỏi kinh nghiệm và trở thành công dân có trách nhiệm...</p>`
  },

  // 7️⃣ Dự án sáng tạo
  "Dự án sáng tạo": {
    date: "13/10/2025",
    author: "Ban biên tập",
    views: 52,
    content: `<p>Dự án sáng tạo giúp trẻ phát triển khả năng tư duy, sáng tạo và đổi mới. Các em tham gia các hoạt động STEM, thiết kế sản phẩm, nghệ thuật và khoa học...</p>
<p>Thông qua dự án, trẻ học cách đưa ý tưởng vào thực tế, thử nghiệm, thất bại và cải thiện, rèn luyện kiên nhẫn và tư duy phản biện...</p>
<p>Dự án sáng tạo kết hợp công nghệ, vật liệu tái chế, lập trình robot, giúp trẻ phát triển kỹ năng 4.0 và tinh thần đổi mới sáng tạo...</p>`
  },

  // 8️⃣ Dự án kỹ năng sống
  "Dự án kỹ năng sống": {
    date: "12/10/2025",
    author: "Ban biên tập",
    views: 48,
    content: `<p>Dự án kỹ năng sống giúp trẻ rèn luyện các kỹ năng cần thiết: tự lập, giải quyết vấn đề, giao tiếp, quản lý cảm xúc...</p>
<p>Trẻ học qua các trò chơi, dự án nhóm, thử thách thực tế, giúp hiểu giá trị hợp tác, trách nhiệm và quản lý cảm xúc...</p>
<p>Kỹ năng sống là nền tảng giúp trẻ tự tin, đối mặt thử thách và thành công trong học tập và cuộc sống sau này...</p>`
  },

  // 9️⃣ Hướng dẫn an toàn trẻ em
  "Hướng dẫn an toàn trẻ em": {
    date: "11/10/2025",
    author: "Ban biên tập",
    views: 43,
    content: `<p>Tài liệu hướng dẫn giáo viên và phụ huynh xây dựng môi trường học tập an toàn, lành mạnh và bổ ích. Bao gồm hướng dẫn phòng tránh tai nạn, xâm hại, bạo lực...</p>
<p>Các phương pháp giáo dục cảm xúc, kỹ năng tự vệ, nhận biết nguy hiểm, đảm bảo sức khỏe tinh thần cho trẻ được trình bày chi tiết...</p>
<p>Phụ huynh và giáo viên học cách đồng hành, giám sát, và tạo môi trường an toàn để trẻ phát triển toàn diện...</p>`
  },

  // 🔟 Phát triển kỹ năng mềm
  "Phát triển kỹ năng mềm": {
    date: "10/10/2025",
    author: "Ban biên tập",
    views: 40,
    content: `<p>Tài liệu hướng dẫn trẻ rèn luyện kỹ năng giao tiếp, sáng tạo, làm việc nhóm, giải quyết vấn đề trong học tập và đời sống...</p>
<p>Kỹ năng mềm được thực hành qua trò chơi, dự án nhóm, thảo luận, giúp trẻ tự tin, linh hoạt và hợp tác tốt hơn...</p>
<p>Đây là những kỹ năng quan trọng, hỗ trợ trẻ thành công trong học tập, xã hội và tương lai nghề nghiệp...</p>`
  },

  // 1️⃣1️⃣ Dự án mẫu cho trẻ em
  "Dự án mẫu cho trẻ em": {
    date: "09/10/2025",
    author: "Ban biên tập",
    views: 38,
    content: `<p>Các dự án mẫu minh họa cho trẻ cách học tập, vui chơi và phát triển toàn diện. Bao gồm dự án STEM, nghệ thuật, thể thao và cộng đồng...</p>
<p>Các dự án được thiết kế thực tế, giúp trẻ rèn luyện kỹ năng mềm, tư duy sáng tạo, kỹ năng xã hội và tinh thần trách nhiệm...</p>
<p>Thông qua các dự án này, trẻ học cách làm việc nhóm, lên kế hoạch, thực hiện, đánh giá và cải tiến, chuẩn bị cho học tập và cuộc sống sau này...</p>`
  }

  }; // end articleDetails

  /* ==================== UI logic (giữ nguyên event binding nhưng nâng cấp hiển thị) ==================== */
  const cards = document.querySelectorAll('.project-card, .doc-card, .hero');
  const modal = document.getElementById('modal');
  const modalImg = document.getElementById('modalImg');
  const modalTitle = document.getElementById('modalTitle');
  const modalContent = document.getElementById('modalContent');
  const modalDate = document.getElementById('modalDate');
  const modalAuthor = document.getElementById('modalAuthor');
  const modalViews = document.getElementById('modalViews');
  const closeModal = document.getElementById('closeModal');
  const backBtn = document.getElementById('backBtn');
  const relatedList = document.getElementById('relatedList');
  const shareBtn = document.getElementById('shareBtn');

  // build a simple related list from articleDetails keys (except current)
  function buildRelated(currentTitle){
    relatedList.innerHTML = '';
    const keys = Object.keys(articleDetails);
    keys.forEach(k => {
      if(k === currentTitle) return;
      // create related item
      const div = document.createElement('div');
      div.className = 'related-item';
      div.dataset.title = k;
      // try to use associated image if exists on a card: find element with data-title
      let thumb = '';
      const el = document.querySelector(`[data-title="${k}"]`);
      if(el && el.dataset.img) thumb = el.dataset.img;
      // fallback tiny placeholder
      if(!thumb) thumb = 'https://picsum.photos/120/80?random=' + (Math.floor(Math.random()*100)+1);

      div.innerHTML = `<img src="${thumb}" alt=""><div style="font-size:0.95rem;color:#333"><strong style="color:var(--primary)">${k}</strong><div style="color:#777;font-size:0.85rem;margin-top:6px">Xem chi tiết</div></div>`;
      div.addEventListener('click', () => {
        openArticleByTitle(k);
      });
      relatedList.appendChild(div);
    });
  }

  function openArticleByTitle(title) {
    modalTitle.textContent = title;
    modalImg.src = document.querySelector(`[data-title="${title}"]`)?.dataset.img || document.querySelector(`[data-title="${title}"] img`)?.src || 'https://picsum.photos/900/450';
    const detail = articleDetails[title];
    if(detail){
      modalDate.textContent = detail.date;
      modalAuthor.textContent = detail.author;
      modalViews.textContent = detail.views || 0;
      modalContent.innerHTML = detail.content;
    } else {
      modalDate.textContent = new Date().toLocaleDateString('vi-VN');
      modalAuthor.textContent = "Ban biên tập";
      modalViews.textContent = "0";
      modalContent.innerHTML = document.querySelector(`[data-title="${title}"]`)?.dataset.content || '';
    }

    buildRelated(title);
    // show modal as fullpage article
    modal.style.display = 'block';
    modal.setAttribute('aria-hidden','false');
    document.body.style.overflow = 'hidden';
  }

  // clicking cards: same as before but open improved modal
  cards.forEach(card => {
    card.addEventListener('click', (e) => {
      // prevent double-click if clicked on inner button link
      const title = card.dataset.title;
      if(title){
        openArticleByTitle(title);
      } else {
        // fallback: if no title set, try inner text
        const t = card.querySelector('h4, h3')?.innerText || card.getAttribute('aria-label') || 'Bài viết';
        openArticleByTitle(t);
      }
    });
  });

  // close (Đóng nút)
  closeModal.addEventListener('click', () => {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden','true');
    document.body.style.overflow = 'auto';
  });

  // back button (nút quay lại) — đóng modal
  backBtn.addEventListener('click', () => {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden','true');
    document.body.style.overflow = 'auto';
  });

  // click outside content to close
  window.addEventListener('click', e => {
    if(e.target === modal){
      modal.style.display = 'none';
      modal.setAttribute('aria-hidden','true');
      document.body.style.overflow = 'auto';
    }
  });

  // share button - giữ nguyên hành vi đơn giản (bạn tùy chỉnh sau)
  shareBtn.addEventListener('click', (e) => {
    e.preventDefault();
    // cố gắng sử dụng Web Share nếu có
    const title = modalTitle.textContent;
    const text = (modalContent.textContent || '').slice(0,150) + '...';
    try {
      if(navigator.share){
        navigator.share({title, text, url: window.location.href});
      } else {
        // fallback: copy link
        navigator.clipboard?.writeText(window.location.href).then(()=> alert('Đã copy link bài viết vào clipboard'));
      }
    } catch(err){ console.warn(err); alert('Không thể chia sẻ.'); }
  });

  // keyboard ESC to close
  window.addEventListener('keydown', (e) => {
    if(e.key === 'Escape' && modal.style.display === 'block'){
      modal.style.display = 'none';
      modal.setAttribute('aria-hidden','true');
      document.body.style.overflow = 'auto';
    }
  });

