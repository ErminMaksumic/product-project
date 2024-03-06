"use client";
import { useEffect, useRef, useState } from "react";
import { useChatApi } from "../context/Chat/ChatContext";
import styles from "./page.module.scss";
import { useUserApi } from "../context/User/UserContext";
import Pusher from "pusher-js";

const ChatPage: React.FC = () => {
  const { getMessages, sendMessage, pusherSendMessage } = useChatApi();
  const { getUser } = useUserApi();
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState<string>("");
  const messagesWrapperRef = useRef<HTMLDivElement>(null);
  const pusherRef = useRef<Pusher>();

  const handleMessageSend = () => {
    if (newMessage.trim() !== "") {
      pusherSendMessage(newMessage);
      sendMessage(newMessage);
      setNewMessage("");
    }
  };

  useEffect(() => {
    const fetchMessagesFromRabbitMQ = async () => {
      try {
        const fetchedMessages = await getMessages();
        const currentUser = await getUser();

        const taggedMessages = fetchedMessages.messages.map(
          (message: Message) => ({
            ...message,
            sent: message.user.name === currentUser?.name,
          })
        );
        setMessages(taggedMessages);
        if (messagesWrapperRef.current) {
          messagesWrapperRef.current.scrollTop =
            messagesWrapperRef.current.scrollHeight;
        }
      } catch (error) {
        console.error("Error fetching messages from RabbitMQ:", error);
      }
    };

    fetchMessagesFromRabbitMQ();
  }, []);

  useEffect(() => {
    const fetchMessages = async () => {
      await getUser();
    };
    fetchMessages();
  }, []);

  useEffect(() => {
    pusherRef.current = new Pusher("bab977fc3ad16d260afc", {
      cluster: "eu",
    });

    const channel = pusherRef.current.subscribe("laravel-app");

    const currentUser = JSON.parse(localStorage.getItem("currentUser") || "{}");

    channel.bind("my-event", (data: any) => {
      const sent = data.message.user.id === currentUser.id;

      setMessages((prevMessages) => [
        ...prevMessages,
        {
          ...data.message,
          sent,
        },
      ]);
    });

    return () => {
      pusherRef.current?.unsubscribe("laravel-app");
    };
  }, []);

  const handleKeyDown = (e: any) => {
    if (e.key === "Enter") {
      handleMessageSend();
    }
  };

  useEffect(() => {
    if (messagesWrapperRef.current) {
      messagesWrapperRef.current.scrollTop =
        messagesWrapperRef.current.scrollHeight;
    }
  }, [messages]);

  return (
    <div className={styles.pageWrapper}>
      <div className={styles.messagesWrapper} ref={messagesWrapperRef}>
        {messages &&
          messages?.map((message: Message, index: any) => (
            <div
              key={index}
              className={`${styles.message} ${
                message?.sent ? styles.sent : styles.received
              }`}
            >
              {!message?.sent ? (
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
          onKeyDown={handleKeyDown}
        />
        <button onClick={handleMessageSend} className={styles.sendButton}>
          Send
        </button>
      </div>
    </div>
  );
};

export default ChatPage;
