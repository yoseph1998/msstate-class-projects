///////////////////////////////////////////////////////////////////////////////
// Main Class File:  client.java
// File:             client.java
// Semester:         Data Comm Fall 2019
//
// Authors:           Yoseph Alabddulwahab  &   Lane Wooten
// NetID:            YSA11  &  JLW1291
// Lecturer's Name:  Maxwell Young
//////////////////////////// 80 columns wide //////////////////////////////////

import java.io.*;
import java.net.*;
import java.util.*;
import java.lang.Math;
/**
 * A Client class to transfer a file to a server using
 * TCP handshakes and UDP file transfers.
 * <p>Bugs: None known about
 *
 * @author Yoseph Alabdulwahab
 * @author Lane Wooten
 */
public class client {
private static InetAddress IPAddress;
private static String fileName;
private static int sendPort, receivePort;
private static int bufferSize = 30;


/**
 *  @param args String array: [address] [port] [filename]
 *  @throws IOException
 */
public static void main(String[] args) throws IOException {
        //ENSURE INPUT IS VALID
        handleInputs(args);

        //-----------------
        //--- Read File ---
        //-----------------
        FileReader fr = new FileReader(fileName);
        String saveFile = "";
        for (int i = 0; (i=fr.read()) != -1;) {
                saveFile += (char) i;
        }

        //-------------------------
        //--- Data Transfer UDP ---
        //-------------------------
        GoBackN gbn = new GoBackN(IPAddress, sendPort, receivePort);
        String[] logs = gbn.send(stringToPPArray(saveFile, bufferSize), 7, 2000);

        //--------------------------
        //-- Write Logs to Files ---
        //--------------------------
        PrintWriter writer = new PrintWriter("clientseqnum.log", "UTF-8");
        writer.print(logs[0]);
        writer.close();

        writer = new PrintWriter("clientack.log", "UTF-8");
        writer.print(logs[1]);
        writer.close();
}

/**
 * Makes sure that input follows specifications or will print an error.
 * @param args String array of size 4 <emulator address> <send port> <receive port> <filename>
 */
public static void handleInputs(String[] args) {
        if (args.length != 4) {
                System.err.println("Usage: java client <emulator address> <send port>" +
                                   " <receive port> <filename>");
                return;
        }
        try {
                IPAddress = InetAddress.getByName(args[0]);
                sendPort = Integer.parseInt(args[1]);
                receivePort = Integer.parseInt(args[2]);
                fileName = args[3];
        } catch (Exception e) {
                System.err.println("Usage: java client <emulator address> <send port>" +
                                   " <receive port> <filename>");
        }
}

/**
 * Converts a string of characters into a PsudoPacket array with a specified segment size for each packet.
 * @param data The data string
 * @param segmentSize the size of data per packet
 * @return Arraylist of PsudoPackets with each packet being a given segmentSize
 */
public static ArrayList<PsudoPacket> stringToPPArray (String data, int segmentSize) {
        ArrayList<String> dataArray = splitString(data, segmentSize);
        ArrayList<PsudoPacket> ppArray = new ArrayList<PsudoPacket>();
        for(int i = 0; i < dataArray.size(); i++) {
                ppArray.add(new PsudoPacket(1, i%8, dataArray.get(i)));
        }
        return ppArray;
}

/**
 * Splits a string into a string array with each sptring being a specified size
 * @param input the input string to split
 * @param segmentSize the size how big should each string be after splitting
 * @return Arraylist of the split strings
 */
private static ArrayList<String> splitString(String input, int segmentSize) {
        ArrayList<String> strArray = new ArrayList<String>();
        String data = "";
        for(int i = 0; i < input.length(); i++) {
                data += input.charAt(i);
                if(i%segmentSize == segmentSize - 1) {
                        strArray.add(data);
                        data = "";
                }
        }
        if(!data.equals(""))
                strArray.add(data);
        return strArray;
}
}
