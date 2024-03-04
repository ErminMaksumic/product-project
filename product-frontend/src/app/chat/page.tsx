"use client";
import { useEffect, useState } from "react";
import { useChatApi } from "../context/Chat/ChatContext";
import styles from "./page.module.scss";
import { useUserApi } from "../context/User/UserContext";

const ChatPage: React.FC = () => {
  const { getMessages, sendMessage } = useChatApi();
  const { getUser } = useUserApi();
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState<string>("");

  const handleMessageSend = () => {
    if (newMessage.trim() !== "") {
      sendMessage(newMessage);
      setNewMessage("");
    }
  };

  useEffect(() => {
    const fetchMessages = async () => {
      const fetchedMessages = await getMessages();
      const currentUser = await getUser();

      const taggedMessages = fetchedMessages.messages.map(
        (message: Message) => ({
          ...message,
          sent: message.user.name === currentUser?.name,
        })
      );
      setMessages(taggedMessages);
    };
    fetchMessages();

    //ADD SOCKET TODO
    const interval = setInterval(fetchMessages, 2500);

    return () => clearInterval(interval);
  }, []);

  return (
    <div className={styles.pageWrapper}>
      <div className={styles.messagesWrapper}>
        {messages?.map((message: Message, index: any) => (
          <div
            key={index}
            className={`${styles.message} ${
              message.sent ? styles.sent : styles.received
            }`}
          >
            {!message.sent ? (
              <div className={styles.imageAndUsername}>
                <div className={styles.imageWrapper}>
                  <img
                    src={
                      "https://media.licdn.com/dms/image/C4E03AQFEcQ_bodPebg/profile-displayphoto-shrink_800_800/0/1656338761095?e=2147483647&v=beta&t=8JbE2wqNR-rYAGnF_otTW66NGxvFqmTF_cC5YIQCiHM"
                    }
                    alt="User"
                    className={styles.image}
                  />
                </div>
                <p className={styles.messageSender}>{message.user.name}</p>
              </div>
            ) : (
              <div className={styles.imageAndUsername}>
                <p className={styles.messageSender}>{message.user.name}</p>
                <div className={styles.imageWrapperSent}>
                  <img
                    src={
                      "https://media.licdn.com/dms/image/C4E03AQFEcQ_bodPebg/profile-displayphoto-shrink_800_800/0/1656338761095?e=2147483647&v=beta&t=8JbE2wqNR-rYAGnF_otTW66NGxvFqmTF_cC5YIQCiHM"
                    }
                    alt="User"
                    className={styles.image}
                  />
                </div>
              </div>
            )}
            <div className={styles.messageContent}>
              <p>{message.message}</p>
            </div>
            <small className={styles.messageDate}>{message.sentDate}</small>
          </div>
        ))}
      </div>
      <div className={styles.messageInputWrapper}>
        <input
          type="text"
          value={newMessage}
          onChange={(e) => setNewMessage(e.target.value)}
          className={styles.messageInput}
        />
        <button onClick={handleMessageSend} className={styles.sendButton}>
          Send
        </button>
      </div>
    </div>
  );
};

export default ChatPage;
