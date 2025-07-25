import React, { useState, useRef, useEffect } from 'react';

/**
 * Hàm này sẽ gọi API backend của bạn để truy vấn MySQL.
 * @param {string} query Câu hỏi của người dùng.
 * @returns {string} Thông tin sản phẩm liên quan dưới dạng chuỗi hoặc thông báo không tìm thấy.
 */
const getProductInfo = async (query) => {
  // Yêu cầu ít nhất 3 ký tự để tìm kiếm hiệu quả hơn
  if (query.trim().length < 3) {
    return "Vui lòng cung cấp thêm chi tiết để tôi tìm kiếm sản phẩm.";
  }

  try {
    // Gọi API backend PHP của bạn
    // Đảm bảo đường dẫn API này là chính xác trên server của bạn
    // Ví dụ: nếu dự án của bạn nằm trong thư mục con 'Apple_Shop', thì đường dẫn sẽ là '/Apple_Shop/api/products.php'
    // Nếu dự án của bạn nằm ở thư mục gốc của localhost, có thể là '/api/products.php'
    const response = await fetch(`/Apple_Shop/api/products.php?query=${encodeURIComponent(query)}`);

    if (!response.ok) {
      // Xử lý lỗi HTTP nếu có (ví dụ: 404 Not Found, 500 Internal Server Error)
      console.error(`HTTP error! status: ${response.status}`);
      return "Đã xảy ra lỗi khi tìm kiếm sản phẩm từ server. Vui lòng thử lại sau.";
    }

    const products = await response.json();

    if (products.length === 0) {
      return "Không tìm thấy thông tin sản phẩm liên quan trong cơ sở dữ liệu.";
    }

    // Định dạng thông tin sản phẩm để gửi cho LLM
    let productInfoString = "Thông tin sản phẩm liên quan từ cơ sở dữ liệu:\n";
    products.forEach((product, index) => {
      productInfoString += `\nSản phẩm ${index + 1}:\n`;
      productInfoString += `- Tên: ${product.name}\n`;
      productInfoString += `- Danh mục: ${product.category_name}\n`; // Sử dụng category_name từ API
      productInfoString += `- Mô tả: ${product.description}\n`;
      // Định dạng giá tiền VNĐ sử dụng Intl.NumberFormat để đảm bảo định dạng chuẩn
      productInfoString += `- Giá: ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.price)}\n`;
      productInfoString += `- Tồn kho: ${product.stock} sản phẩm\n`;
      if (product.variants && product.variants.length > 0) {
        productInfoString += `- Biến thể: `;
        product.variants.forEach((variant, vIndex) => {
          // Hiển thị thuộc tính và giá của biến thể
          productInfoString += `${variant.attributes} (Giá: ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(variant.variant_price)})`;
          productInfoString += `${vIndex < product.variants.length - 1 ? '; ' : ''}`;
        });
        productInfoString += '\n';
      }
    });

    return productInfoString;
  } catch (error) {
    console.error("Lỗi khi gọi API tìm kiếm sản phẩm:", error);
    return "Đã xảy ra lỗi khi tìm kiếm sản phẩm. Vui lòng thử lại sau.";
  }
};

// Main App component for the Chatbot
const App = () => {
  // State để lưu trữ các tin nhắn trong cuộc trò chuyện
  const [messages, setMessages] = useState([]);
  // State để lưu trữ nội dung của ô nhập liệu hiện tại
  const [input, setInput] = useState('');
  // State để quản lý trạng thái tải (đang chờ phản hồi từ bot)
  const [isLoading, setIsLoading] = useState(false);

  // Ref để tự động cuộn đến cuối khung chat
  const messagesEndRef = useRef(null);

  // Effect để cuộn xuống tin nhắn mới nhất mỗi khi messages state thay đổi
  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages]);

  /**
   * Xử lý việc gửi tin nhắn.
   * Thêm tin nhắn của người dùng vào chat, sau đó gọi API để lấy thông tin sản phẩm
   * và gửi prompt đến LLM để nhận phản hồi.
   */
  const handleSendMessage = async () => {
    if (input.trim() === '') return; // Không gửi tin nhắn rỗng

    const userMessage = { sender: 'user', text: input.trim() };
    setMessages((prevMessages) => [...prevMessages, userMessage]); // Thêm tin nhắn người dùng vào chat
    setInput(''); // Xóa nội dung ô nhập liệu
    setIsLoading(true); // Hiển thị trạng thái tải

    try {
      // 1. Lấy thông tin sản phẩm liên quan từ API backend PHP
      // Hàm này sẽ trả về một chuỗi chứa thông tin sản phẩm hoặc thông báo lỗi/không tìm thấy
      const productContext = await getProductInfo(userMessage.text);

      // 2. Xây dựng lời nhắc (prompt) cho LLM, bao gồm thông tin sản phẩm
      // Lấy lịch sử trò chuyện hiện tại để cung cấp ngữ cảnh cho LLM
      let chatHistory = messages.map(msg => ({
        role: msg.sender === 'user' ? 'user' : 'model',
        parts: [{ text: msg.text }]
      }));

      // Hướng dẫn hệ thống cho LLM về vai trò và cách phản hồi
      const systemInstruction = `Bạn là một trợ lý chatbot chuyên về sản phẩm của cửa hàng Apple. Nhiệm vụ của bạn là trả lời các câu hỏi của khách hàng về các sản phẩm dựa trên thông tin được cung cấp.
      
      Nếu có thông tin sản phẩm liên quan được cung cấp, hãy sử dụng nó để đưa ra câu trả lời chi tiết, chính xác và hữu ích.
      Nếu khách hàng hỏi về giá, hãy cung cấp giá bằng VNĐ và sử dụng định dạng số có dấu chấm phân cách hàng nghìn (ví dụ: 34.990.000 VNĐ).
      Nếu không có thông tin sản phẩm liên quan, hãy nói rõ rằng bạn không tìm thấy sản phẩm đó trong cơ sở dữ liệu của mình và hỏi khách hàng có muốn tìm kiếm sản phẩm khác không hoặc cung cấp thêm chi tiết.
      Hãy giữ câu trả lời ngắn gọn, thân thiện và đi thẳng vào vấn đề.`;

      // Kết hợp hướng dẫn hệ thống, ngữ cảnh sản phẩm và câu hỏi của người dùng vào một prompt duy nhất
      const userPromptWithContext = `${systemInstruction}\n\n${productContext}\n\nCâu hỏi của khách hàng: ${userMessage.text}`;

      // Thêm prompt cuối cùng của người dùng (có ngữ cảnh) vào lịch sử chat
      chatHistory.push({ role: "user", parts: [{ text: userPromptWithContext }] });

      // Chuẩn bị payload cho API Gemini
      const payload = { contents: chatHistory };
      // API key sẽ được tự động cung cấp bởi môi trường Canvas cho gemini-2.0-flash
      const apiKey = ""; 
      const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`;

      // Gọi API Gemini để lấy phản hồi của bot
      const response = await fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const result = await response.json();

      let botResponseText = "Xin lỗi, tôi không thể tạo phản hồi lúc này.";

      // Kiểm tra cấu trúc phản hồi từ API Gemini và lấy nội dung tin nhắn
      if (
        result.candidates &&
        result.candidates.length > 0 &&
        result.candidates[0].content &&
        result.candidates[0].content.parts &&
        result.candidates[0].content.parts.length > 0
      ) {
        botResponseText = result.candidates[0].content.parts[0].text;
      } else {
        console.error("Cấu trúc phản hồi API không mong muốn:", result);
        if (result.error && result.error.message) {
            botResponseText = `Lỗi từ API: ${result.error.message}`;
        }
      }

      // Thêm phản hồi của bot vào chat
      setMessages((prevMessages) => [...prevMessages, { sender: 'bot', text: botResponseText }]);

    } catch (error) {
      console.error("Lỗi khi gọi API Gemini hoặc API sản phẩm:", error);
      // Hiển thị thông báo lỗi cho người dùng
      setMessages((prevMessages) => [
        ...prevMessages,
        { sender: 'bot', text: 'Đã xảy ra lỗi khi kết nối với chatbot. Vui lòng thử lại.' }
      ]);
    } finally {
      setIsLoading(false); // Ẩn trạng thái tải
    }
  };

  return (
    <div className="flex flex-col h-screen bg-gray-100 font-inter">
      {/* Header của Chatbot */}
      <header className="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4 shadow-md rounded-b-xl">
        <h1 className="text-2xl font-bold text-center">Chatbot Hỗ Trợ Sản Phẩm</h1>
      </header>

      {/* Khu vực hiển thị tin nhắn */}
      <div className="flex-1 overflow-y-auto p-4 space-y-4">
        {messages.length === 0 && (
          <div className="text-center text-gray-500 mt-10">
            Chào mừng bạn! Tôi là chatbot hỗ trợ sản phẩm. Hãy hỏi tôi về iPhone, MacBook, Apple Watch, v.v. nhé!
          </div>
        )}
        {messages.map((msg, index) => (
          <div
            key={index}
            className={`flex ${msg.sender === 'user' ? 'justify-end' : 'justify-start'}`}
          >
            <div
              className={`max-w-xs md:max-w-md lg:max-w-lg p-3 rounded-lg shadow-md ${
                msg.sender === 'user'
                  ? 'bg-blue-500 text-white rounded-br-none'
                  : 'bg-white text-gray-800 rounded-bl-none'
              }`}
            >
              <p className="text-sm">{msg.text}</p>
            </div>
          </div>
        ))}
        {/* Hiển thị hiệu ứng typing indicator khi bot đang tải */}
        {isLoading && (
          <div className="flex justify-start">
            <div className="max-w-xs md:max-w-md lg:max-w-lg p-3 rounded-lg shadow-md bg-white text-gray-800 rounded-bl-none">
              <div className="flex items-center space-x-2">
                <div className="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style={{ animationDelay: '0s' }}></div>
                <div className="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style={{ animationDelay: '0.1s' }}></div>
                <div className="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style={{ animationDelay: '0.2s' }}></div>
              </div>
            </div>
          </div>
        )}
        <div ref={messagesEndRef} /> {/* Điểm neo để cuộn */}
      </div>

      {/* Khu vực nhập liệu tin nhắn */}
      <div className="p-4 bg-white border-t border-gray-200 shadow-lg rounded-t-xl flex items-center space-x-3">
        <input
          type="text"
          className="flex-1 p-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
          placeholder="Hỏi tôi về sản phẩm..."
          value={input}
          onChange={(e) => setInput(e.target.value)}
          onKeyPress={(e) => {
            if (e.key === 'Enter' && !isLoading) {
              handleSendMessage();
            }
          }}
          disabled={isLoading} // Vô hiệu hóa input khi đang tải
        />
        <button
          onClick={handleSendMessage}
          className="bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          disabled={isLoading} // Vô hiệu hóa nút gửi khi đang tải
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            className="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            strokeWidth={2}
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              d="M14 5l7 7m0 0l-7 7m7-7H3"
            />
          </svg>
        </button>
      </div>
    </div>
  );
};

export default App;
