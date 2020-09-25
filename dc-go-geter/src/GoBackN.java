import java.net.DatagramPacket;
import java.net.DatagramSocket;
import java.net.InetAddress;
import java.util.*;
import java.net.Socket;
import java.lang.Math;
import java.io.IOException;

/**
 * A class to implement the GoBackN protocol. Uses a dynamically sliding window .
 *
 * @author Yoseph Alabdulwahab
 * @author Lane Wooten
 */
public class GoBackN {
private InetAddress ipAddress;
private int sendPort, receivePort;

/**
 * @param ip The ip address to send data to.
 * @param sendPort The port to send data to.
 * @param receivePort The port to receive data from.
 */
public GoBackN(InetAddress ip, int sendPort, int receivePort) {
        this.ipAddress = ip;
        this.sendPort = sendPort;
        this.receivePort = receivePort;
}

/**
 * Uses the Go Back N protocol to receive data from a client. returns data as a string.
 * It will ignore all out of order packets and wait to receive an in order packet.
 *
 * @return a string of the data received.
 */
public String[] receive() {
        String arrivalLog = "";
        String dataLog = "";

        try {
                LinkedList<PsudoPacket> dataReceived = new LinkedList<PsudoPacket>();

                DatagramSocket dSocket = new DatagramSocket(receivePort);
                byte[] receive = new byte[1024];
                byte[] send = new byte[1024];
                DatagramPacket packet = null;
                PsudoPacket recPacket;
                PsudoPacket ackPacket;

                while(true) {
                        //Recieve Byte
                        packet = new DatagramPacket(receive, receive.length);
                        dSocket.receive(packet);
                        recPacket = new PsudoPacket(receive);
                        arrivalLog += recPacket.getSeqNum() + "\n";

                        //check for eot
                        if(recPacket.getType() == 3) {
                                System.out.println(recPacket);
                                System.out.println("------EOT------");
                                break;
                        }

                        if(dataReceived.size() == 0 && recPacket.getSeqNum() == 0) {
                                dataReceived.add(recPacket);
                                dataLog += recPacket.getData();
                                System.out.println("First:\n" + recPacket + "\n");

                                //Send Ack
                                ackPacket = recPacket.getAckPacket();
                                send = ackPacket.getByteArray();
                                packet = new DatagramPacket(send, send.length, ipAddress, sendPort);
                                dSocket.send(packet);
                        } else if((dataReceived.peekLast().getSeqNum()+1)%8 == recPacket.getSeqNum()) {
                                dataReceived.add(recPacket);
                                dataLog += recPacket.getData();
                                System.out.println(recPacket + "\n");


                                //Send Ack
                                ackPacket = recPacket.getAckPacket();
                                send = ackPacket.getByteArray();
                                packet = new DatagramPacket(send, send.length, ipAddress, sendPort);
                                dSocket.send(packet);
                        } else {
                                System.out.println("Received packet out of order. Expecting sequence number "
                                                   + ((dataReceived.peekLast().getSeqNum()+1)%8) + " received sequence number "
                                                   + recPacket.getSeqNum() + "\n");
                                System.out.println("SeqNums are: " + recPacket.getSeqNum() + ":" + dataReceived.peekLast().getSeqNum());
                        }

                        // Clear the buffer after every message.
                        receive = new byte[1024];
                        send = new byte[1024];
                }
                dSocket.close();
        } catch (IOException e) {
                System.out.println(e);
        }
        String[] logs = new String[2];
        logs[0] = dataLog;
        logs[1] = arrivalLog;
        return logs;
}

/**
 * Implements the GoBackN protocol for the client.
 * The window is a sliding window and will slide as long as it is not full.
 * (i.e. it will not wait till all packets in the window are acked to add more
 * packets to the window).
 *
 * @param packets An arraylist of the packets to send
 * @param windowSize The size of the sending window
 * @param timeout The timeout duration to wait for an ack
 *
 * @return if it successfully sent all data and did not crash.
 */
public String[] send(ArrayList<PsudoPacket> packets, int windowSize, int timeout) {
        //Reorganize Data
        ArrayDeque<PsudoPacket> dataAcked, dataSent, dataReady, dataWaiting;
        int numPackets = packets.size();
        dataAcked = new ArrayDeque<PsudoPacket> (numPackets);
        dataSent = new ArrayDeque<PsudoPacket> (windowSize);
        dataReady = new ArrayDeque<PsudoPacket> (windowSize);
        dataWaiting = new ArrayDeque<PsudoPacket> (numPackets);

        String sentLog = "";
        String ackLog = "";

        for(int i = 0; i<packets.size(); i++) {
                dataWaiting.add(packets.get(i));
        }
        for(int i = 0; i < windowSize && i < numPackets; i++) {
                dataReady.add(dataWaiting.remove());
        }

        //Start Communication
        try {
                //Connection Related Fields
                DatagramSocket dSocket = new DatagramSocket(receivePort); // Establish Connection
                DatagramPacket packet = null;

                byte[] receive = new byte[1024];

                //Logic related fields
                int nWindows = (int)Math.ceil((double)packets.size() / windowSize);
                int windowFloor, windowCeiling;
                boolean isWindowFull = false;
                long timer = System.currentTimeMillis();

                //Start Window
                while(dataAcked.size() < numPackets) {
                        //send if data is in ready queue
                        if(dataReady.size() != 0) {
                                //Send from head of ready queue
                                byte[] peek = dataReady.peek().getByteArray();
                                packet = new DatagramPacket(peek, peek.length, ipAddress, sendPort);
                                dSocket.send(packet);
                                sentLog += dataReady.peek().getSeqNum() + "\n"; //log
                                //Pull off of ready queue and add to sent queue
                                dataSent.add(dataReady.remove());
                        }

                        //move waiting to ready queue if ready queue has space and the waiting queue is not empty
                        if(dataSent.size() + dataReady.size() < windowSize && dataWaiting.size() != 0) {
                                dataReady.add(dataWaiting.remove());
                        }

                        //get acks
                        try {
                                packet = new DatagramPacket(receive, receive.length);
                                dSocket.setSoTimeout(1);
                                dSocket.receive(packet);
                                PsudoPacket recPsudoPacket = new PsudoPacket(receive);
                                ackLog += recPsudoPacket.getSeqNum() + "\n"; //log

                                if(recPsudoPacket.getSeqNum() == dataSent.peek().getSeqNum()) {
                                        dataAcked.add(dataSent.remove());
                                        timer = System.currentTimeMillis();
                                        System.out.println(recPsudoPacket + "\n");
                                } else {
                                        System.out.println("Expecting SeqNum " + dataSent.peek().getSeqNum()
                                                           + " received SeqNum " + recPsudoPacket.getSeqNum());
                                        System.out.println("Going back and retransmitting from expected SeqNum\n");
                                        while(true) {
                                                if(dataSent.peek() == null)
                                                        break;
                                                else if (recPsudoPacket.getSeqNum() != dataSent.peek().getSeqNum())
                                                        dataReady.addFirst(dataSent.remove());
                                                else {
                                                        dataAcked.add(dataSent.remove());
                                                        timer = System.currentTimeMillis();
                                                        break;
                                                }
                                        }
                                }
                        } catch (IOException e) {
                                //System.out.println("--Did not block recieve call--");
                        }

                        if(System.currentTimeMillis()-timer >= timeout) {
                                while(dataSent.size() != 0)
                                        dataReady.add(dataSent.remove());
                                timer = System.currentTimeMillis();
                                System.out.println("Wops... Timer expired, retransmitting all unacked packets.\n");
                        }
                        // Clear the buffer after every message.
                        receive = new byte[1024];
                }


                //Send EOT
                int lastSeqNum = dataAcked.peekLast().getSeqNum();
                PsudoPacket eotPacket = new PsudoPacket((lastSeqNum+1)%8);
                sentLog += eotPacket.getSeqNum() + "\n"; //log
                //Send it
                byte[] eotBytes = eotPacket.getByteArray();
                packet = new DatagramPacket(eotBytes, eotBytes.length, ipAddress, sendPort);
                dSocket.send(packet);

                System.out.println(eotPacket);
                System.out.println("------Sent EOT------");
                //Close Datagram Socket
                dSocket.close();
        } catch (IOException e) {
                System.out.println("\nMain Exception: \n\n"+e);
        }
        String[] logStrings = new String[2];
        logStrings[0] = sentLog;
        logStrings[1] = ackLog;
        return logStrings;
}

/**
 * Checks for end of transmission ascii value within the byte array
 * @param a byte array to check for eot in
 * @return if the eot character is detected
 */
public static boolean eot(byte[] a) {
        if (a == null)
                return false;
        for(int i = 0; i < a.length; i++)
        {
                if(a[i] == 4)
                        return true;
        }
        return false;
}

}
