import java.io.*;

public class PsudoPacket {

private int type, seqNum, size;
private String data;

/**
 * Constructor for EOT Packet
 * @param bufferSize: int for transmission size
 * @param seqNum int for the sequence number
 */
public PsudoPacket (int seqNum) {
        type = 3;
        this.size = -1;
        this.seqNum = seqNum;
        data = null;
}

/**
 * Construct the packet using a serialized byte array of the packet class
 * @param bytes serialized byte array
 */
public PsudoPacket (byte[] bytes) {
        packet pack = bytesToPacket(bytes);
        type = pack.getType();
        seqNum = pack.getSeqNum();
        this.size = pack.getLength();
        data = pack.getData();
}

/**
 * Construct the packet using data arguments of the packet contents
 * @param type int for packet type
 * @param seqNum int for the sequence number
 * @param data The data for the packet
 */
public PsudoPacket (int type, int seqNum, String data) {
        this.type = type;
        this.seqNum = seqNum;
        if(data != null)
                this.size = data.length();
        else
                this.size = -1;
        this.data = data;
}

/**
 *  The ack packet that would be expected for this packet.
 * @return a PsudoPacket object that represents the respective ack packet
 */
public PsudoPacket getAckPacket () {
        if(type != 1)
                return null;
        return new PsudoPacket(0, seqNum, null);
}

/**
 *
 * @return a serialized byte array of Dr.Young's packet this class wraps.
 */
public byte[] getByteArray() {
        try {
                packet pack;
                if(data == null)
                        pack = new packet(type, seqNum, 0, null);
                else
                        pack = new packet(type, seqNum, size, data);

                ByteArrayOutputStream oSt = new ByteArrayOutputStream();
                ObjectOutputStream ooSt = new ObjectOutputStream(oSt);
                ooSt.writeObject(pack);
                ooSt.flush();
                byte[] sendBuf = new byte[1024];
                sendBuf = oSt.toByteArray();

                return sendBuf;
        } catch (Exception e) {
                System.err.println("Failed to serialize packet");
                return null;
        }
}

/**
 *
 * @return an object of Dr.Young's packet that this class wraps.
 */
public packet getYoungPacket() {
        return new packet(type, seqNum, data.length(), data);
}

/**
 * @param bytes Deserialized byte array to convert to packet class
 */
public static packet bytesToPacket(byte[] bytes) {
        try{
                packet pack;
                ByteArrayInputStream bis = new ByteArrayInputStream(bytes);
                ObjectInputStream in = new ObjectInputStream(bis);
                pack = (packet) in.readObject();
                return pack;
        } catch (EOFException e) {
                e.printStackTrace();
                return null;
        } catch (IOException e) {
                System.err.println(e);
                return null;
        } catch (ClassNotFoundException e) {
                System.err.println(e);
                return null;
        }
}

/**
 * @param packet packet type
 */
public void setType(int type) {
        this.type = type;
}

/**
 * @param seqNum packet sequence number
 */
public void setSeqNum(int seqNum) {
        this.seqNum = seqNum;
}

/**
 * @param data packet data
 */
public void setData(String data) {
        this.data = data;
}

/**
 * @return packet type
 */
public int getType() {
        return type;
}

/**
 * @return packet sequence number
 */
public int getSeqNum() {
        return seqNum;
}

/**
 * @return packet data size
 */
public int getSize() {
        return size;
}

/**
 * @return packet data
 */
public String getData() {
        return data;
}

@Override
public String toString() {
        if(data != null)
                return "type: " + type + "  seqnum: " + seqNum + "  length: " + data.length() + "\ndata: " + data;
        return "type: " + type + "  seqnum: " + seqNum + "  length: 0\ndata: null";

}

}
