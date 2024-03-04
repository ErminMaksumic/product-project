import axios from "axios";

const authToken = localStorage.getItem("accessToken");
axios.defaults.headers.common["Authorization"] = `Bearer ${authToken}`;

export async function sendMessage(message: string): Promise<void> {
  try {
    await axios.post(`${process.env.NEXT_PUBLIC_URL}/api/v1/message`, {
      message,
    });
  } catch (error) {
    console.error("Error sending message:", error);
    throw error;
  }
}

export async function getMessages(): Promise<any> {
  try {
    const response = await axios.get<any>(
      `${process.env.NEXT_PUBLIC_URL}/api/v1/messages`
    );
    return response.data;
  } catch (error) {
    console.error("Error fetching messages:", error);
    throw error;
  }
}
